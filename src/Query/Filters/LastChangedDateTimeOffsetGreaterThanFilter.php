<?php

namespace Poweroffice\Query\Filters;

final class LastChangedDateTimeOffsetGreaterThanFilter extends AbstractDateTimeFilter
{
    public function name(): string
    {
        return 'lastChangedDateTimeOffsetGreaterThan';
    }
}
