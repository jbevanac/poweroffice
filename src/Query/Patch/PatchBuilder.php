<?php

namespace Poweroffice\Query\Patch;

final class PatchBuilder
{
    /** @var JsonPatchOperation[] */
    private array $operations = [];

    public function replace(string $path, mixed $value): self
    {
        $this->operations[] = new JsonPatchOperation(
            op: 'replace',
            path: '/' . ltrim($path, '/'),
            value: $value,
        );

        return $this;
    }

    public function add(string $path, mixed $value): self
    {
        $this->operations[] = new JsonPatchOperation(
            op: 'add',
            path: '/' . ltrim($path, '/'),
            value: $value,
        );

        return $this;
    }

    public function copy(string $from, string $to): self
    {
        $this->operations[] = new JsonPatchOperation(
            op: 'copy',
            path: '/' . ltrim($to, '/'),
            from: '/' . ltrim($from, '/'),
        );
        return $this;
    }

    public function remove(string $path): self
    {
        $this->operations[] = new JsonPatchOperation(
            op: 'remove',
            path: '/' . ltrim($path, '/'),
        );

        return $this;
    }

    public function toArray(): array
    {
        return array_map(
            fn (JsonPatchOperation $op) => $op->toArray(),
            $this->operations
        );
    }

    public function isEmpty(): bool
    {
        return empty($this->operations);
    }
}
