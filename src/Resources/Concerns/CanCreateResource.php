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
    public function createResource(ModelInterface $model, string $path): ModelInterface|ProblemDetail|null
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
        $responseData = $this->decodeJsonResponse($response);

        if (Status::RESOURCE_CREATED == $response->getStatusCode()) {
            return $model::make(data: $responseData);
        }

        // Need to handle 400, 401, 404, 403, and 429
        return ProblemDetail::make(data: $responseData);
    }
}