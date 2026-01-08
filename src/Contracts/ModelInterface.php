<?php

namespace Poweroffice\Contracts;

interface ModelInterface
{
    public function toJson(): string;

    public static function make(array $data): self;
}
