<?php

namespace Poweroffice\Exceptions;

class UriTooLongException extends PowerofficeException
{
    public function __construct(int $length, int $max = 2000)
    {
        parent::__construct(
            sprintf(
                'Request URI too long (%d chars). PowerOffice API rejects URLs longer than %d characters.',
                $length,
                $max
            )
        );
    }
}
