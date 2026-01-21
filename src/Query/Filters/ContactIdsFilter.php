<?php

namespace Poweroffice\Query\Filters;

final class ContactIdsFilter extends AbstractStringAbleFilter
{
    public function name(): string
    {
        return 'contactIds';
    }
}
