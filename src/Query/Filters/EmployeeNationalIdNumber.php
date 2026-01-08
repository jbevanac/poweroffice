<?php

namespace Poweroffice\Query\Filters;

use Poweroffice\Contracts\FilterInterface;

class EmployeeNationalIdNumber implements FilterInterface
{
    private array $nationalIds;
    private const string NAME = 'employeeNationalIdNumber';
    public function __construct(string|array $nationalIds)
    {
        $this->nationalIds = is_array($nationalIds) ? $nationalIds : [$nationalIds];
    }

    public function toQuery(): array
    {
        return [
            self::NAME => implode(',', $this->nationalIds),
        ];
    }
}
