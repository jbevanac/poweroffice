<?php

namespace Poweroffice\Resources\Concerns;

use Poweroffice\Contracts\ModelInterface;
use Poweroffice\Contracts\ResourceInterface;
use Poweroffice\Enum\Method;
use Poweroffice\Enum\Status;
use Poweroffice\Exceptions\ApiException;
use Poweroffice\Model\ProblemDetail;

/**
 * @mixin ResourceInterface
 */
trait CanCreateResource
{
    /**
     * @throws ApiException
     */
    public function createResource(ModelInterface $model, array|string $path): ModelInterface|ProblemDetail
    {
        $request = $this->request(
            method: Method::POST,
            url: $path,
        );

        $request = $this->attachPayLoad(
            request: $request,
            payload: $model->toJson(),
        );

        $response = $this->sendRequest($request);
        $data = $this->decodeJsonResponse($response);

        if (Status::RESOURCE_CREATED->value === $response->getStatusCode()) {
            return $model::make(data: $data);
        }

        // Need to handle 400, 401, 404, 403, and 429
        return ProblemDetail::make(data: $data);
    }
}