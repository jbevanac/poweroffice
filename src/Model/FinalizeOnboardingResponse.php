<?php

namespace Poweroffice\Model;

use Poweroffice\Contracts\ModelInterface;

final class FinalizeOnboardingResponse implements ModelInterface
{
    use ModelTrait;

    public function __construct(
        public ?array $onboardedClientsInformation = null,
        public ?string $userEmail = null,
    ) {
    }
}
