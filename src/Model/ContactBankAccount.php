<?php

namespace Poweroffice\Model;

use Poweroffice\Contracts\ModelInterface;

final class ContactBankAccount implements ModelInterface
{
    use ModelTrait;

    public function __construct(
        public ?string $bankAccountNumber = null,
        public ?string $bankCode = null,
        public ?string $bicSwift = null,
        public ?int $contactId = null,
        public ?string $countryCode = null,
        public ?string $createDateTimeOffset = null,
        public ?int $id = null,
        public ?bool $isActive = null,
        public ?string $lastChangedDateTimeOffset = null,
    ) {
    }
}
