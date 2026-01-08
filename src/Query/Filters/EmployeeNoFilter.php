<?php

namespace Poweroffice\Query\Filters;

use Poweroffice\Contracts\FilterInterface;

class EmployeeNoFilter implements FilterInterface
{
    private array $numbers;
    private const string NAME = 'employeeNos';

    public function __construct(int|array $numbers)
    {
        $this->numbers = is_array($numbers) ? $numbers : [$numbers];
    }

    public function toQuery(): array
    {
        return [
            self::NAME => implode(',', $this->numbers),
        ];
    }
}
