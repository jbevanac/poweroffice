<?php

namespace Poweroffice;

use Http\Client\Common\Plugin\AuthenticationPlugin;
use Http\Client\Common\Plugin\ErrorPlugin;
use Http\Client\Common\Plugin\HeaderAppendPlugin;
use Http\Client\Common\Plugin\RetryPlugin;
use Http\Client\Common\PluginClient;
use Http\Discovery\Composer\Plugin;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Http\Message\Authentication\Bearer;
use Poweroffice\Contracts\SDKInterface;
use Poweroffice\Enum\Method;
use Poweroffice\Plugins\UserAgentPlugin;
use Poweroffice\Resources\ClientIntegrationInformationResource;
use Poweroffice\Resources\ContactBankAccountsResource;
use Poweroffice\Resources\EmployeeResource;
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
        private readonly string $clientKey,
        private readonly string $subscriptionKey,
        private readonly ?ClientInterface $customClient = null,
        private readonly ?CacheInterface $cache = null,
        private readonly string $cacheKey = 'poweroffice_access_token',
        private array $plugins = [],
    ) {
        $this->withPlugins($this->plugins);
    }

    private function loadOrCreateAccessToken(): void
    {
        if ($this->accessToken && $this->accessTokenExpiresAt && $this->accessTokenExpiresAt > time()) {
            return;
        }

        if ($this->cache) {
            $tokenData = $this->cache->get($this->cacheKey);
            if ($tokenData && $tokenData['expires_at'] > time()) {
                $this->accessToken = $tokenData['token'];
                $this->accessTokenExpiresAt = $tokenData['expires_at'];
                return;
            }
        }

        $tokenData = $this->authenticate();
        $this->accessToken = $tokenData['token'];
        $this->accessTokenExpiresAt = $tokenData['expires_at'];

        $this->cache?->set(
            key: $this->cacheKey,
            value: $tokenData,
            ttl: $tokenData['expires_at'] - time()
        );
    }

    private function authenticate(): array
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

        // Wrap with PluginClient only for safe plugins
        $plugins = array_filter($this->plugins, fn($p) => $p instanceof UserAgentPlugin);
        $client = new PluginClient($this->client(), $plugins);

        $response = $client->sendRequest($request);

        $body = (string) $response->getBody();
        $data = json_decode($body, true);

        if (!isset($data['access_token'])) {
            throw new \RuntimeException(
                'Unable to authenticate with PowerOffice API: ' . json_encode($data)
            );
        }

        return [
            'token'      => $data['access_token'],
            'expires_at' => time() + $data['expires_in'],
        ];
    }

    public function getAccessToken(): string
    {
        $this->loadOrCreateAccessToken();

        return $this->accessToken;
    }

    public function getSubscriptionKey(): string
    {
        return $this->subscriptionKey;
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
            new AuthenticationPlugin(
                new Bearer(
                    token: $this->getAccessToken(),
                )
            ),
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
