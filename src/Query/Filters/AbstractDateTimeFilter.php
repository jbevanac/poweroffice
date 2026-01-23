<?php

namespace Poweroffice\Query\Filters;

use DateTimeInterface;
use DateTimeImmutable;
use Poweroffice\Contracts\FilterInterface;
use Poweroffice\Exceptions\InvalidFilterValueException;

abstract class AbstractDateTimeFilter implements FilterInterface
{
    /** @var DateTimeInterface[] */
    protected array $dateTimes;

    /**
     * @throws InvalidFilterValueException
     */
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

    /**
     * @throws InvalidFilterValueException
     */
    protected function normalize(DateTimeInterface|string $value): DateTimeInterface
    {
        if ($value instanceof DateTimeInterface) {
            return $value;
        }


        try {
            return new DateTimeImmutable($value);
        } catch (\Exception $e) {
            throw new InvalidFilterValueException(
                sprintf(
                    'Invalid datetime filter value "%s". Expected DateTimeInterface or valid datetime string.',
                    $value
                ),
                previous: $e
            );
        }
    }

    protected function format(DateTimeInterface $dateTime): string
    {
        // PowerOffice-compatible format
        return $dateTime->format('Y-m-d H:i:s.u P');
    }
}