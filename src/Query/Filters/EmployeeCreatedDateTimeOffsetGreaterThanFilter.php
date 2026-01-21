<?php

namespace Poweroffice\Query\Filters;

final class EmployeeCreatedDateTimeOffsetGreaterThanFilter extends AbstractDateTimeFilter
{
    public function name(): string
    {
        return 'employeeCreatedDateTimeOffsetGreaterThan';
    }
}
