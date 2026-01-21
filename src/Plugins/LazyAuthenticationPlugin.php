<?php

namespace Poweroffice\Plugins;


use Http\Client\Common\Plugin;
use Http\Promise\Promise;
use Poweroffice\PowerofficeSDK;
use Psr\Http\Message\RequestInterface;

final readonly class LazyAuthenticationPlugin implements Plugin
{
    public function __construct(
        private PowerofficeSDK $sdk,
    ) {
    }

    public function handleRequest(RequestInterface $request, callable $next, callable $first): Promise
    {
        $token = $this->sdk->loadOrCreateAccessToken();
        $request = $request->withHeader('Authorization', 'Bearer ' . $token);

        return $next($request);
    }
}
