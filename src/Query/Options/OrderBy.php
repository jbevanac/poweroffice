<?php

namespace Poweroffice\Query\Options;

use Poweroffice\Enum\Direction;

class OrderBy
{
    public function __construct(
        public string $field,
        public Direction $direction = Direction::ASC
    ) {
        $this->field = ucfirst($this->field);
    }

    public function toQuery(): string
    {
        return $this->field.' '.$this->direction->value;
    }

}
