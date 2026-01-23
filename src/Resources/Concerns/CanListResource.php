<?php

namespace Poweroffice\Resources\Concerns;

use Poweroffice\Contracts\ModelInterface;
use Poweroffice\Contracts\ResourceInterface;
use Poweroffice\Enum\Method;
use Poweroffice\Enum\Status;
use Poweroffice\Exceptions\ApiException;
use Poweroffice\Exceptions\FailedToSendRequestException;
use Poweroffice\Model\ProblemDetail;
use Poweroffice\Query\Options\QueryOptions;
use Ramsey\Collection\Collection;

/**
 * @mixin ResourceInterface
 */
trait CanListResource
{
    /**
     * @throws FailedToSendRequestException
     * @throws ApiException
     */
    public function listResource(string $modelClass, array|string $path, array $filters = [], ?QueryOptions $queryOptions = null): ModelInterface|Collection
    {
        if (!is_subclass_of($modelClass, ModelInterface::class)) {
            throw new \InvalidArgumentException("$modelClass must implement ModelInterface");
        }

        $request = $this->applyFilters(
            request: $this->request(
                method: Method::GET,
                url: $path,
            ),
            filters: $filters,
            queryOptions: $queryOptions,
        );

        $response = $this->sendRequest($request);

        if (Status::NO_CONTENT->value === $response->getStatusCode()) {
            return $this->createCollection($modelClass, []);
        }

        $data = $this->decodeJsonResponse($response);

        if (Status::OK->value !== $response->getStatusCode()) {
            return ProblemDetail::make(data: $data);
        }

        return $this->createCollection($modelClass, $data);
    }
}
