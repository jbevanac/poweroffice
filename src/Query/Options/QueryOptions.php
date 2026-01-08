<?php

namespace Poweroffice\Query\Options;

final class QueryOptions
{
    public function __construct(
        public ?array $fields = null,
        public ?string $orderBy = null,
        public ?int $pageNumber = null,
        public ?int $pageSize = null,
        public ?bool $useDatabaseValidation = null,
    ) {}

    public function toQuery(): array
    {
        $query = [];

        if ($this->fields) {
            $query['Fields'] = implode(',', $this->fields);
        }

        if ($this->orderBy) {
            $query['OrderBy'] = $this->orderBy;
        }

        if ($this->pageNumber !== null) {
            $query['PageNumber'] = $this->pageNumber;
        }

        if ($this->pageSize !== null) {
            $query['PageSize'] = $this->pageSize;
        }

        if ($this->useDatabaseValidation !== null) {
            $query['UseDatabaseValidation'] = $this->useDatabaseValidation ? 'true' : 'false';
        }

        return $query;
    }
}
