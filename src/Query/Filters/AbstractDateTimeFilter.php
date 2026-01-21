<?php

namespace Poweroffice\Query\Filters;

use DateTimeInterface;
use DateTimeImmutable;
use Poweroffice\Contracts\FilterInterface;

abstract class AbstractDateTimeFilter implements FilterInterface
{
    /** @var DateTimeInterface[] */
    protected array $dateTimes;

    public function __construct(DateTimeInterface|string|array $values)
    {
        $values = is_array($values) ? $values : [$values];

        $this->dateTimes = array_map(
            fn ($value) => $this->normalize($value),
            $values
        );
    }

    abstract protected function name(): string;

    public function toQuery(): array
    {
        return [
            $this->name() => implode(
                ',',
                array_map([$this, 'format'], $this->dateTimes)
            ),
        ];
    }

    protected function normalize(DateTimeInterface|string $value): DateTimeInterface
    {
        if ($value instanceof DateTimeInterface) {
            return $value;
        }

        return new DateTimeImmutable($value);
    }

    protected function format(DateTimeInterface $dateTime): string
    {
        // PowerOffice-compatible format
        return $dateTime->format('Y-m-d H:i:s.u P');
    }
}