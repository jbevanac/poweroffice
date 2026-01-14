<?php

namespace Poweroffice\Query\Patch;

final readonly class JsonPatchOperation
{
    public function __construct(
        public string  $op,
        public string  $path,
        public mixed   $value = null,
        public ?string $from = null,
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'op' => $this->op,
            'path' => $this->path,
            'value' => $this->value,
            'from' => $this->from,
        ], fn ($v) => $v !== null);
    }
}
