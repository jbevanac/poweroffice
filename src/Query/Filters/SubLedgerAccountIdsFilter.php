<?php

namespace Poweroffice\Query\Filters;

abstract class SubLedgerAccountIdsFilter extends AbstractStringAbleFilter
{
    public function name(): string
    {
        return 'subLedgerAccountIds';
    }
}
