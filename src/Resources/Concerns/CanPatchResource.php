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
use Poweroffice\Query\Patch\PatchBuilder;

/**
 * @mixin ResourceInterface
 */
trait CanPatchResource
{
    /**
     * @throws FailedToDecodeJsonResponseException
     * @throws UriTooLongException
     * @throws PowerofficeException
     * @throws FailedToSendRequestException
     */
    public function patchResource(string $modelClass, PatchBuilder $patchBuilder, array|string $path): ProblemDetail|ModelInterface
    {
        if (!is_subclass_of($modelClass, ModelInterface::class)) {
            throw new \InvalidArgumentException("$modelClass must implement ModelInterface");
        }

        $request = $this->request(
            method: Method::PATCH,
            url: $path,
        );

        $request = $this->attachPayLoad(
            request: $request,
            payload: json_encode($patchBuilder->toArray()),
        );

        $response = $this->sendRequest($request);
        $data = $this->decodeJsonResponse($response);

        if (Status::OK->value === $response->getStatusCode()) {
            return $modelClass::make(data: $data);
        }

        return ProblemDetail::make(data: $data);
    }
}
