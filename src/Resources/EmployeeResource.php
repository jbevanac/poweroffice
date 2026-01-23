<?php

namespace Poweroffice\Resources;

use Poweroffice\Exceptions\PowerofficeException;
use Poweroffice\Model\Employee;
use Poweroffice\Model\EmployeeBankAccounts;
use Poweroffice\Model\MailAddress;
use Poweroffice\Model\ProblemDetail;
use Poweroffice\Query\Options\QueryOptions;
use Poweroffice\Query\Patch\PatchBuilder;
use Poweroffice\Resources\Concerns\CanCreateCollection;
use Poweroffice\Resources\Concerns\CanCreateRequest;
use Poweroffice\Resources\Concerns\CanCreateResource;
use Poweroffice\Resources\Concerns\CanFindResource;
use Poweroffice\Resources\Concerns\CanListResource;
use Poweroffice\Resources\Concerns\CanPatchResource;
use Ramsey\Collection\Collection;
use Poweroffice\Contracts\ResourceInterface;
use Poweroffice\Resources\Concerns\CanAccessSDK;

final class EmployeeResource implements ResourceInterface
{
    private const string PATH = 'Employees';

    use CanAccessSDK;
    use CanCreateRequest;
    use CanCreateCollection;
    use CanCreateResource;
    use CanPatchResource;
    use CanFindResource;
    use CanListResource;

    /**
     * @param array{
     *     firstName: string,
     *     lastName: string,
     *     emailAddress?: string,
     *     dateOfBirth?: string,
     *     departmentCode?: string,
     *     departmentId?: int,
     *     endDate?: string,
     *     externalImportReference?: string,
     *     externalNumber?: int,
     *     gender?: string,
     *     hiredDate?: string,
     *     internationalIdCountryCode?: string,
     *     internationalIdNumber?: string,
     *     internationalIdReportToAltinn?: bool,
     *     internationalIdType?: string,
     *     isArchived?: bool,
     *     jobTitle?: string,
     *     locationCode?: string,
     *     locationId?: int,
     *     mailAddress?: MailAddress,
     *     managerEmployeeNo?: int,
     *     nationalIdNumber?: string,
     *     number?: int,
     *     phoneNumber?: string,
     *     salaryBankAccountId?: int,
     *     startDate?: string,
     *     subledgerAccountId?: int,
     *     travelExpenseBankAccountId?: int,
     * } $data
     *
     * @throws PowerofficeException
     */
    public function create(array $data): Employee|ProblemDetail
    {
        $employee = Employee::make($data);

        return $this->createResource(
            model: $employee,
            path: self::PATH,
        );
    }

    /**
     * @throws PowerofficeException
     */
    public function patch(PatchBuilder $patchBuilder, string|int $id): Employee|ProblemDetail
    {
        return $this->patchResource(
            modelClass: Employee::class,
            patchBuilder: $patchBuilder,
            path: [self::PATH, $id],
        );
    }

    /**
     * @throws PowerofficeException
     */
    public function patchBankAccounts(PatchBuilder $patchBuilder, string|int $id): EmployeeBankAccounts|ProblemDetail
    {
        return $this->patchResource(
            modelClass: EmployeeBankAccounts::class,
            patchBuilder: $patchBuilder,
            path: [self::PATH, $id, 'BankAccounts'],
        );
    }

    /**
     * @throws PowerofficeException
     */
    public function find(int $id): Employee|ProblemDetail
    {
        return $this->findResource(
            modelClass: Employee::class,
            path: [self::PATH, $id],
        );
    }

    /**
     * @throws PowerofficeException
     */
    public function list(array $filters = [], ?QueryOptions $queryOptions = null): Collection|ProblemDetail
    {
        return $this->listResource(
            modelClass: Employee::class,
            path: self::PATH,
            filters: $filters,
            queryOptions: $queryOptions,
        );
    }

}
