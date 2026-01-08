<?php

namespace Poweroffice\Resources\Concerns;

use Poweroffice\Contracts\ResourceInterface;
use Poweroffice\PowerofficeSDK;

/**
 * @mixin ResourceInterface
 */
trait CanAccessSDK
{
    public function __construct(
        private readonly PowerofficeSDK $sdk,
    ) {
    }

    public function getSdk(): PowerofficeSDK
    {
        return $this->sdk;
    }
}