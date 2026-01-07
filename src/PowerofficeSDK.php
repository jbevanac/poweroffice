<?php

namespace Poweroffice;

use Http\Client\Common\Plugin\AuthenticationPlugin;
use Http\Client\Common\Plugin\ErrorPlugin;
use Http\Client\Common\Plugin\RetryPlugin;
use Http\Client\Common\PluginClient;
use Http\Discovery\Composer\Plugin;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Http\Message\Authentication\Bearer;
use Poweroffice\Contracts\SDKInterface;
use Poweroffice\Enum\Method;
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
    private ?string $access_token;

    public function __construct(
        private readonly string $baseUrl,
        private readonly string $applicationKey,
        private readonly string $clientKey,
        private readonly string $subscriptionKey,
        private ?ClientInterface $client = null,
        private readonly ?CacheInterface $cache = null,
        private readonly string $cacheKey = 'poweroffice_access_token',
        private array $plugins = [],
    ) {
        $this->loadOrCreateSessionToken();
    }

    private function loadOrCreateSessionToken(): void
    {
        if ($this->cache) {
            $tokenData = $this->cache->get($this->cacheKey);
            if ($tokenData) {
                $this->access_token = $tokenData['token'];
                return;
            }
        }

        $tokenData = $this->authenticate();

        var_dump($tokenData);
        $this->cache?->set(
            key: $this->cacheKey,
            value: $tokenData,
            ttl: 1100
        );

        $this->access_token = $tokenData['token'];
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

        $response = $this->client()->sendRequest($request);

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

    public function getToken(): string
    {
        return base64_encode("0:$this->access_token");
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

        return $this;
    }

    public function defaultPlugins(): array
    {
        return [
            new RetryPlugin(),
            new ErrorPlugin(),
            new AuthenticationPlugin(
                new Bearer(
                    token: $this->access_token,
                )
            )
        ];
    }

    public function client(): ClientInterface
    {
        if ($this->client !== null) {
            return $this->client;
        }

        $this->client = new PluginClient(
            client: Psr18ClientDiscovery::find(),
            plugins: $this->plugins,
        );

        return $this->client;
    }

    public function setClient(ClientInterface $client): PowerofficeSDK
    {
        $this->client = $client;

        return $this;
    }

    public function getUrl(): string
    {
        return $this->baseUrl;
    }

    public static function getSerializer(): Serializer
    {
        return new Serializer(
            [new BackedEnumNormalizer(), new ObjectNormalizer(null, null, null, new ReflectionExtractor())],
            [new JsonEncoder()]
        );

    }


    /* RESOURCES */

}
