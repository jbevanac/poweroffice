<?php

namespace Poweroffice\Serializer;

use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

final class PascalCaseToCamelCaseNameConverter implements NameConverterInterface
{
    public function normalize(string $propertyName, ?string $class = null, ?string $format = null, array $context = []): string
    {
        return ucfirst($propertyName);
    }

    public function denormalize(string $propertyName, ?string $class = null, ?string $format = null, array $context = []): string
    {
        return lcfirst($propertyName);
    }
}
