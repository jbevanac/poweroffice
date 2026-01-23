<?php

namespace Poweroffice\Model;

use Poweroffice\Contracts\ModelInterface;

final class OnboardedClientInformation implements ModelInterface
{
    use ModelTrait;

    public function __construct(
        public ?string $clientKey = null,
        public ?string $clientName = null,
        public ?string $clientOrganizationNumber = null,
    ) {
    }
}
