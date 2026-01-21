<?php

namespace Poweroffice\Query\Filters;

use Poweroffice\Contracts\FilterInterface;

abstract class AbstractStringAbleFilter implements FilterInterface
{
    protected array $stringAbles;

    public function __construct(int|string|array $stringsAble)
    {
        $this->stringAbles = is_array($stringsAble) ? $stringsAble : [$stringsAble];
    }

    abstract protected function name(): string;

    public function toQuery(): array
    {
        return [
            $this->name() => implode(',', $this->stringAbles),
        ];
    }
}
