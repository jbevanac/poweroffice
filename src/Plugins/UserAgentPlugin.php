<?php

namespace Poweroffice\Plugins;


use Http\Client\Common\Plugin;
use Http\Promise\Promise;
use Psr\Http\Message\RequestInterface;

final readonly class UserAgentPlugin implements Plugin
{
    public function __construct(
        private string $userAgent,
    ) {
    }

    public function handleRequest(RequestInterface $request, callable $next, callable $first): Promise
    {
        $request = $request->withHeader('User-Agent', $this->userAgent);

        return $next($request);
    }
}
