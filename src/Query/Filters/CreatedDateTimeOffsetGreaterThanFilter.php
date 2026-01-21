<?php

namespace Poweroffice\Query\Filters;

final class CreatedDateTimeOffsetGreaterThanFilter extends AbstractDateTimeFilter
{
    public function name(): string
    {
        return 'createdDateTimeOffsetGreaterThan';
    }
}
