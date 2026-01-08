<?php

namespace Poweroffice\Contracts;

interface FilterInterface
{
    public function toQuery(): array;
}
