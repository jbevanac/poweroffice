<?php

namespace Poweroffice\Contracts;

use Psr\Http\Client\ClientInterface;
use Poweroffice\PowerofficeSDK;

interface SDKInterface
{
    public function withPlugins(array $plugins): PowerofficeSDK;

    public function defaultPlugins(): array;

    public function client(): ClientInterface;

    public function setClient(ClientInterface $client): PowerofficeSDK;

    public function getUrl(): string;

    public function getToken(): string;
}