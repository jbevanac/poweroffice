<?php

namespace Poweroffice\Query\Filters;

class ContactIdsFilter extends AbstractStringAbleFilter
{
    public function name(): string
    {
        return 'contactIds';
    }
}
