<?php

namespace Poweroffice\Query\Filters;

final class EmployeeNationalIdNumberFilter extends AbstractStringAbleFilter
{
    public function name(): string
    {
        return 'employeeNationalIdNumber';
    }
}
