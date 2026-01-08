<?php

namespace Poweroffice\Resources;

use Poweroffice\Model\Employee;
use Poweroffice\Model\ProblemDetail;
use Poweroffice\Query\Options\QueryOptions;
use Poweroffice\Resources\Concerns\CanCreateCollection;
use Poweroffice\Resources\Concerns\CanCreateRequest;
use Poweroffice\Resources\Concerns\CanCreateResource;
use Poweroffice\Resources\Concerns\CanFindResource;
use Poweroffice\Resources\Concerns\CanListResource;
use Ramsey\Collection\Collection;
use Poweroffice\Contracts\ResourceInterface;
use Poweroffice\Exceptions\ApiException;
use Poweroffice\Resources\Concerns\CanAccessSDK;

final class EmployeeResource implements ResourceInterface
{
    use CanAccessSDK;
    use CanCreateRequest;
    use CanCreateCollection;
    use CanCreateResource;
    // use CanUpdateResource;
    use CanFindResource;
    use CanListResource;

    /**
     * @param array{
     *     firstName: string,
     *     lastName: string,
     *     emailAddress?: string,
     * } $data
     * @throws ApiException
     */
    public function create(array $data): Employee|ProblemDetail
    {
        $employee = Employee::make($data);

        return $this->createResource(
            model: $employee,
            path: 'employees',
        );
    }

    /**
     * @throws ApiException
     */
    // public function update(array $data): Employee|ProblemDetail
    // {
    //     /** @var Employee $customer */
    //     $employee = Employee::make($data);
    //
    //     return $this->updateResource(
    //         model: $employee,
    //         path: 'customer/' . $customer->id,
    //     );
    // }

    /**
     * @throws ApiException
     */
    public function find(int $id): Employee|ProblemDetail
    {
        return $this->findResource(
            modelClass: Employee::class,
            path: 'employees/' . $id,
        );
    }

    /**
     * @throws ApiException
     */
    public function findRaw(int $id): array
    {
        return $this->findResource(
            modelClass: Employee::class,
            path: 'employees/' . $id,
            raw: true,
        );
    }

    /**
     * @throws ApiException
     */
    public function list(array $filters = [], ?QueryOptions $queryOptions = null): Collection
    {
        return $this->listResource(
            modelClass: Employee::class,
            path: 'employees',
            filters: $filters,
            queryOptions: $queryOptions,
        );
    }

}
