<?php

namespace Poweroffice\Model;

use Poweroffice\Contracts\ModelInterface;

final class InitiateOnboardingResponse implements ModelInterface
{
    use ModelTrait;

    public function __construct(
        public ?string $temporaryUrl = null,
        public ?string $validUntilDateTimeOffset = null,
    ) {
    }
}
