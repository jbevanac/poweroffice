<?php

namespace Poweroffice\Model;

use Poweroffice\Contracts\ModelInterface;

final class OffboardingDTO implements ModelInterface
{
    use ModelTrait;

    public function __construct(
        public ?string $message = null,
        public ?bool $removeFactoring = null,
        public ?bool $resetInvoiceFinancing = null,
        public ?bool $success = null,
    ) {
    }
}
