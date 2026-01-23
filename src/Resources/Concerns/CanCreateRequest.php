<?php

namespace Poweroffice\Resources\Concerns;

use Http\Discovery\Psr17FactoryDiscovery;
use Poweroffice\Contracts\FilterInterface;
use Poweroffice\Exceptions\FailedToDecodeJsonResponseException;
use Poweroffice\Exceptions\FailedToSendRequestException;
use Poweroffice\Exceptions\UriTooLongException;
use Poweroffice\Query\Options\QueryOptions;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Poweroffice\Contracts\ResourceInterface;
use Poweroffice\Enum\Method;

/**
 * @mixin ResourceInterface
 */
trait CanCreateRequest
{
    /**
     * PowerOffice API rejects URLs longer than ~2000 characters
     * and returns a misleading 404.
     */
    private const int MAX_URI_LENGTH = 2000;

    public function prepareUrl(string $url): string
    {
        $baseUrl = str_replace('https://', '', rtrim($this->getSdk()->getUrl(), '/'));
        $url = str_replace('https://', '', trim($url, '/'));

        // If the url starts with baseUrl, remove the duplicated base
        if (str_starts_with($url, $baseUrl)) {
            $url = ltrim(substr($url, strlen($baseUrl)), '/');
        }

        return 'https://' . $baseUrl . '/' . $url;
    }

    public function request(Method $method, array|string $url, array $query = [], ?string $body = null, array $headers = []): RequestInterface
    {
        if (is_array($url)) {
            $url = implode('/', array_map(fn($s) => trim((string)$s, '/'), $url));
        }

        $uri = $this->prepareUrl($url);

        if (!empty($query)) {
            $uri .= '?' . http_build_query($query);
        }

        $length = strlen($uri);
        if ($length > self::MAX_URI_LENGTH) {
            throw new UriTooLongException($length, self::MAX_URI_LENGTH);
        }

        $requestFactory = Psr17FactoryDiscovery::findRequestFactory();
        $streamFactory = Psr17FactoryDiscovery::findStreamFactory();

        $request = $requestFactory->createRequest($method->value, $uri);

        if ($body !== null) {
            $request = $request->withBody(
                $streamFactory->createStream($body)
            );
        }

        foreach ($headers as $name => $value) {
            $request = $request->withHeader($name, $value);
        }

        return $request;
    }

    /**
     * @param RequestInterface $request
     * @param array<int,FilterInterface|scalar|array> $filters
     * @param QueryOptions|null $queryOptions
     * @return RequestInterface
     */
    public function applyFilters(RequestInterface $request, array $filters, ?QueryOptions $queryOptions = null): RequestInterface
    {
        $query = [];

        foreach ($filters as $key => $value) {
            if ($value instanceof FilterInterface) {
                $query = array_merge($query, $value->toQuery());
            } elseif (is_array($value)) {
                $query[$key] = implode(',', $value);
            } else {
                $query[$key] = $value;
            }
        }

        if ($queryOptions) {
            $query = array_merge($query, $queryOptions->toQuery());
        }

        $uri = $request->getUri()->withQuery(
            query: http_build_query($query),
        );

        return $request->withUri(
            uri: $uri,
            preserveHost: true,
        );
    }

    public function attachPayLoad(RequestInterface $request, string $payload): RequestInterface
    {
        return $request->withBody(
            body: Psr17FactoryDiscovery::findStreamFactory()->createStream(
                content: $payload,
            )
        );
    }

    /**
     * @throws FailedToSendRequestException
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        try {
            return $this->getSdk()->client()->sendRequest(
                request: $request,
            );
        } catch (\Throwable $e) {
            throw new FailedToSendRequestException(
                message: 'Failed to send request.',
                previous: $e,
            );
        }
    }

    /**
     * @throws FailedToDecodeJsonResponseException
     */
    public function decodeJsonResponse(ResponseInterface $response): array
    {
        try {
            return json_decode(
                json: $response->getBody()->getContents(),
                associative: true,
                flags: JSON_THROW_ON_ERROR,
            );
        } catch (\JsonException $e) {
            throw new FailedToDecodeJsonResponseException(
                message: 'Invalid JSON response from API',
                code: $e->getCode(),
                previous: $e,
            );
        }
    }
}
