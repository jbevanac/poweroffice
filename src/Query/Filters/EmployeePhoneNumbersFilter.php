<?php

namespace Poweroffice\Query\Filters;

final class EmployeePhoneNumbersFilter extends AbstractStringAbleFilter
{
    public function name(): string
    {
        return 'employeePhoneNumbers';
    }
}
