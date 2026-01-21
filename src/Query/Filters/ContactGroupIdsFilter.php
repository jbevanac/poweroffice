<?php

namespace Poweroffice\Query\Filters;

final class ContactGroupIdsFilter extends AbstractStringAbleFilter
{
    public function name(): string
    {
        return 'contactGroupIds';
    }
}
