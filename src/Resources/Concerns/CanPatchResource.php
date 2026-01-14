<?php

namespace Poweroffice\Resources\Concerns;

use Poweroffice\Contracts\ModelInterface;
use Poweroffice\Contracts\ResourceInterface;
use Poweroffice\Enum\Method;
use Poweroffice\Enum\Status;
use Poweroffice\Exceptions\ApiException;
use Poweroffice\Model\ProblemDetail;
use Poweroffice\Query\Patch\PatchBuilder;

/**
 * @mixin ResourceInterface
 */
trait CanPatchResource
{
    /**
     * @throws ApiException
     */
    public function patchResource(string $modelClass, PatchBuilder $patchBuilder, string $path): ProblemDetail|ModelInterface
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
