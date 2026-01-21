<?php

namespace Poweroffice\Query\Filters;

final class EmployeeEmailsFilter extends AbstractStringAbleFilter
{
    public function name(): string
    {
        return 'employeeEmails';
    }
}
