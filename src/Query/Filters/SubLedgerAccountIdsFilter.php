<?php

namespace Poweroffice\Query\Filters;

final class SubLedgerAccountIdsFilter extends AbstractStringAbleFilter
{
    public function name(): string
    {
        return 'subLedgerAccountIds';
    }
}
