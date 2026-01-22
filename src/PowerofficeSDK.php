<?php

namespace Poweroffice;

use Http\Client\Common\Plugin;
use Http\Client\Common\Plugin\ErrorPlugin;
use Http\Client\Common\Plugin\HeaderAppendPlugin;
use Http\Client\Common\Plugin\RetryPlugin;
use Http\Client\Common\PluginClient;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Poweroffice\Contracts\SDKInterface;
use Poweroffice\Enum\Method;
use Poweroffice\Enum\Status;
use Poweroffice\Exceptions\InvalidClientException;
use Poweroffice\Exceptions\InvalidGrantException;
use Poweroffice\Exceptions\InvalidRequestException;
use Poweroffice\Exceptions\PowerofficeException;
use Poweroffice\Exceptions\RateLimitException;
use Poweroffice\Exceptions\UnauthorizedClientException;
use Poweroffice\Exceptions\UnsupportedGrantTypeException;
use Poweroffice\Model\TokenResponse;
use Poweroffice\Plugins\LazyAuthenticationPlugin;
use Poweroffice\Plugins\UserAgentPlugin;
use Poweroffice\Resources\ClientIntegrationInformationResource;
use Poweroffice\Resources\ContactBankAccountsResource;
use Poweroffice\Resources\EmployeeResource;
use Poweroffice\Resources\OnboardingResource;
use Poweroffice\Serializer\PascalCaseToCamelCaseNameConverter;
use Psr\Http\Client\ClientInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\BackedEnumNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;


final class PowerofficeSDK implements SDKInterface
{
    private const string AUTH_ROUTE = '/OAuth/Token';
    private const string VERSION = 'v2';
    private ?string $accessToken = null;
    private ?int $accessTokenExpiresAt = null;
    private ?ClientInterface $client = null;

    public function __construct(
        private readonly string $baseUrl,
        private readonly string $applicationKey,
        private readonly string $subscriptionKey,
        private readonly string $clientKey,
        private readonly ?ClientInterface $customClient = null,
        private readonly ?CacheInterface $cache = null,
        private readonly string $cacheKey = 'poweroffice_access_token',
        private array $plugins = [],
    ) {
        $this->withPlugins($this->plugins);
    }

    public function loadOrCreateAccessToken(): string
    {
        // Check in-memory token first
        if ($this->accessToken && $this->accessTokenExpiresAt && $this->accessTokenExpiresAt > time()) {
            return $this->accessToken;
        }

        // Check cache
        if ($this->cache && $this->cacheKey) {
            $tokenData = $this->cache->get($this->cacheKey);
            if ($tokenData instanceof TokenResponse && $tokenData->expiresAt > time()) {
                $this->accessToken = $tokenData->accessToken;
                $this->accessTokenExpiresAt = $tokenData->expiresAt;
                return $this->accessToken;
            }
        }

        // Authenticate
        $tokenResponse = $this->authenticate();
        $this->accessToken = $tokenResponse->accessToken;
        $this->accessTokenExpiresAt = $tokenResponse->expiresAt;

        // Cache it
        $this->cache?->set(
            key: $this->cacheKey,
            value: $tokenResponse,
            ttl: $tokenResponse->expiresAt - time(),
        );

        return $this->accessToken;
    }

    private function authenticate(): TokenResponse
    {
        $uri = rtrim($this->baseUrl, '/').self::AUTH_ROUTE;

        $requestFactory = Psr17FactoryDiscovery::findRequestFactory();
        $streamFactory = Psr17FactoryDiscovery::findStreamFactory();

        // Build Authorization header
        $basicAuth = base64_encode(
            $this->applicationKey . ':' . $this->clientKey
        );

        $body = http_build_query([
            'grant_type' => 'client_credentials',
        ]);

        $request = $requestFactory
            ->createRequest(Method::POST->value, $uri)
            ->withHeader('Authorization', 'Basic ' . $basicAuth)
            ->withHeader('Ocp-Apim-Subscription-Key', $this->subscriptionKey)
            ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->withHeader('Accept', 'application/json')
            ->withBody($streamFactory->createStream($body));

        $response = $this->safeClient()->sendRequest($request);

        $body = (string) $response->getBody();
        $data = json_decode($body, true);
        $status = $response->getStatusCode();

        if (Status::OK->value === $status) {
            return TokenResponse::make($data);
        }

        if (Status::TO_MANY_REQUESTS->value === $status) {
            $retryAfter = 0;
            if (isset($data['message']) && preg_match('/(\d+) seconds?/', $data['message'], $matches)) {
                $retryAfter = (int)$matches[1];
            }
            throw new RateLimitException($data['message'] ?? 'Rate limit exceeded', $retryAfter);
        }

        if (Status::INVALID_REQUEST->value === 400 && isset($data['error'])) {
            throw match ($data['error']) {
                'invalid_request' => new InvalidRequestException($data['error_description'] ?? 'Invalid request'),
                'invalid_client' => new InvalidClientException($data['error_description'] ?? 'Invalid client credentials'),
                'invalid_grant' => new InvalidGrantException($data['error_description'] ?? 'Invalid grant'),
                'unauthorized_client' => new UnauthorizedClientException($data['error_description'] ?? 'Unauthorized client'),
                'unsupported_grant_type' => new UnsupportedGrantTypeException($data['error_description'] ?? 'Unsupported grant type'),
                default => new PowerofficeException($data['error_description'] ?? 'Unknown error'),
            };
        }
        throw new PowerofficeException(
            'Unable to authenticate with PowerOffice API: ' . json_encode($data)
        );
    }

    /**
     * @param array<int, Plugin> $plugins
     * @return $this
     */
    public function withPlugins(array $plugins): PowerofficeSDK
    {
        $this->plugins = array_merge(
            $this->defaultPlugins(),
            $plugins,
        );

        $this->client = null;

        return $this;
    }

    public function defaultPlugins(): array
    {
        return [
            new RetryPlugin(),
            new ErrorPlugin(),
            new LazyAuthenticationPlugin($this),
            new HeaderAppendPlugin([
                'Ocp-Apim-Subscription-Key' => $this->subscriptionKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]),
        ];
    }

    public function client(): ClientInterface
    {
        if (null !== $this->client) {
            return $this->client;
        }

        $httpClient = $this->customClient ?? Psr18ClientDiscovery::find();
        $this->client = new PluginClient(
            client: $httpClient,
            plugins: $this->plugins,
        );

        return $this->client;
    }

    public function safeClient(): ClientInterface
    {
        $httpClient = $this->customClient ?? Psr18ClientDiscovery::find();

        $safePlugins = array_filter(
            $this->plugins,
            fn($plugin) => $plugin instanceof UserAgentPlugin
        );

        return new PluginClient($httpClient, $safePlugins);
    }

    public function getUrl(): string
    {
        return $this->baseUrl . '/' . self::VERSION;
    }

    public static function getSerializer(): Serializer
    {
        return new Serializer(
            normalizers: [
                new BackedEnumNormalizer(),
                new ObjectNormalizer(null, new PascalCaseToCamelCaseNameConverter(), null, new ReflectionExtractor()),
            ],
            encoders: [
                new JsonEncoder(),
            ],
        );
    }

    /* RESOURCES */
    // public function onboarding(): OnboardingResource
    // {
    //     return new OnboardingResource(
    //         sdk: $this,
    //     );
    // }

    public function employees(): EmployeeResource
    {
        return new EmployeeResource(
            sdk: $this,
        );
    }

    public function clientIntegrationInformation(): ClientIntegrationInformationResource
    {
        return new ClientIntegrationInformationResource(
            sdk: $this,
        );
    }

    public function contactBankAccounts(): ContactBankAccountsResource
    {
        return new ContactBankAccountsResource(
            sdk: $this,
        );
    }
}
