<?php

namespace Poweroffice\Model;

use Poweroffice\Contracts\ModelInterface;

class ProblemDetail implements ModelInterface
{
    use ModelTrait;

    public function __construct(
        public ?string $detail,
        public ?string $instance,
        public ?int $status,
        public ?string $title,
        public ?string $type,
    ) {
    }
}