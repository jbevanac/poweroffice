<?php

namespace Poweroffice\Query\Filters;

abstract class ExternalImportReferenceFilter extends AbstractStringAbleFilter
{
    public function name(): string
    {
        return 'externalImportReference';
    }
}
