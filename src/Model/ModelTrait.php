<?php

namespace Poweroffice\Model;

use Poweroffice\Contracts\ModelInterface;
use Poweroffice\PowerofficeSDK;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Poweroffice\Exceptions\ApiException;
use Poweroffice\Exceptions\SerializerException;

trait ModelTrait
{
    /**
     * @throws ApiException
     */
    public function toJson(): string
    {
        try {
            return PowerofficeSDK::getSerializer()->serialize(
                $this,
                'json',
                [AbstractObjectNormalizer::SKIP_NULL_VALUES => true]
            );
        } catch (\Throwable $e) {
            throw new SerializerException('Serialization failed: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @param array $data
     * @return ModelInterface
     * @throws ApiException
     */
    public static function make(array $data): ModelInterface
    {
        try {
            return PowerofficeSDK::getSerializer()->denormalize(
                data: $data,
                type: static::class,
            );
        } catch (\Exception $e) {
            throw new SerializerException('Deserialization failed: ' . $e->getMessage(), 0, $e);
        }
    }
}