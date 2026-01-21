<?php

namespace Poweroffice\Query\Filters;

final class BankAccountNumbersFilter extends AbstractStringAbleFilter
{
    public function name(): string
    {
        return 'bankAccountNumbers';
    }
}
