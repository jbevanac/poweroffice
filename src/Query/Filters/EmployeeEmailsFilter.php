<?php

namespace Poweroffice\Query\Filters;

use Poweroffice\Contracts\FilterInterface;

class EmployeeEmailsFilter implements FilterInterface
{
    private array $emails;
    private const string NAME = 'employeeEmails';

    public function __construct(string|array $emails)
    {
        $this->emails = is_array($emails) ? $emails : [$emails];
    }

    public function toQuery(): array
    {
        return [
            self::NAME => implode(',', $this->emails),
        ];
    }
}
