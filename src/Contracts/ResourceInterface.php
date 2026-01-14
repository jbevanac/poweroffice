<?php

namespace Poweroffice\Contracts;

use Poweroffice\PowerofficeSDK;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Poweroffice\Enum\Method;

interface ResourceInterface
{
    public function request(Method $method, string $url, array $query = [], ?string $body = null, array $headers = []): RequestInterface;

    public function getSdk(): PowerofficeSDK;

    public function attachPayLoad(RequestInterface $request, string $payload): RequestInterface;

    public function sendRequest(RequestInterface $request): ResponseInterface;
}
