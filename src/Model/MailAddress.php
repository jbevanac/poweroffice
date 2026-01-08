<?php

namespace Poweroffice\Model;

use Poweroffice\Contracts\ModelInterface;

class MailAddress implements ModelInterface
{
    use ModelTrait;

    public function __construct(
        public ?int $id,
        public ?string $addressLine1,
        public ?string $addressLine2,
        public ?string $city,
        public ?string $countryCode,
        public ?string $externalCode,
        public ?string $lastChangedDateTimeOffset,
        public ?string $zipCode,
    ) {
    }
}