<?php

namespace Poweroffice\Resources\Concerns;

use Poweroffice\Contracts\ModelInterface;
use Poweroffice\Contracts\ResourceInterface;
use Poweroffice\Enum\Method;
use Poweroffice\Enum\Status;
use Poweroffice\Exceptions\FailedToDecodeJsonResponseException;
use Poweroffice\Exceptions\FailedToSendRequestException;
use Poweroffice\Exceptions\PowerofficeException;
use Poweroffice\Exceptions\UriTooLongException;
use Poweroffice\Model\ProblemDetail;

/**
 * @mixin ResourceInterface
 */
trait CanFindResource
{
    /**
     * @throws FailedToDecodeJsonResponseException
     * @throws UriTooLongException
     * @throws PowerofficeException
     * @throws FailedToSendRequestException
     */
    public function findResource(string $modelClass, array|string $path, bool $raw = false): ModelInterface|ProblemDetail|array
    {
        if (!is_subclass_of($modelClass, ModelInterface::class)) {
            throw new \InvalidArgumentException("$modelClass must implement ModelInterface");
        }

        $request = $this->request(
            method: Method::GET,
            url: $path,
        );

        $response = $this->sendRequest($request);
        $data = $this->decodeJsonResponse($response);

        if ($raw) {
            return $data;
        }

        if (Status::OK->value === $response->getStatusCode()) {
            return $modelClass::make(data: $data);
        }

        return ProblemDetail::make(data: $data);
    }
}
