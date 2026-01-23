<?php

namespace Poweroffice\Model;

use Poweroffice\Contracts\ModelInterface;

final class FinalizeOnboardingResponse implements ModelInterface
{
    use ModelTrait;

    public function __construct(
        /** @var OnboardedClientInformation[] */
        public array $onboardedClientsInformation = [],
        public ?string $userEmail = null,
    ) {
    }
}
