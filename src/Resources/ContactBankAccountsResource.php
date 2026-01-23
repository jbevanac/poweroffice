<?php

namespace Poweroffice\Resources;

use Poweroffice\Exceptions\PowerofficeException;
use Poweroffice\Model\ContactBankAccount;
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

final class ContactBankAccountsResource implements ResourceInterface
{
    private const string PATH = 'ContactBankAccounts';

    use CanAccessSDK;
    use CanCreateRequest;
    use CanCreateCollection;
    use CanCreateResource;
    use CanPatchResource;
    use CanFindResource;
    use CanListResource;

    /**
     * @param array{
     *     bankAccountNumber?: string,
     *     bankCode?: string,
     *     bicSwift?: string,
     *     contactId?: int,
     *     countryCode?: string,
     *     createDateTimeOffset?: string,
     *     id?: int,
     *     isActive?: bool,
     *     lastChangedDateTimeOffset?: string,
     * } $data
     *
     * @throws PowerofficeException
     */
    public function create(array $data, string|int $contactId): ContactBankAccount|ProblemDetail
    {
        $contactBankAccount = ContactBankAccount::make($data);

        return $this->createResource(
            model: $contactBankAccount,
            path: [self::PATH, $contactId],
        );
    }

    /**
     * @throws PowerofficeException
     */
    public function patch(PatchBuilder $patchBuilder, string|int $contactId, string|int $contactBankAccountId): ContactBankAccount|ProblemDetail
    {
        return $this->patchResource(
            modelClass: ContactBankAccount::class,
            patchBuilder: $patchBuilder,
            path: [self::PATH, $contactId, $contactBankAccountId],
        );

    }

    /**
     * @throws PowerofficeException
     */
    public function find(string|int $contactId, string|int $contactBankAccountId): ContactBankAccount|ProblemDetail
    {
        return $this->findResource(
            modelClass: ContactBankAccount::class,
            path: [self::PATH, $contactId, $contactBankAccountId],
        );
    }

    /**
     * @throws PowerofficeException
     */
    public function list(array $filters = [], ?QueryOptions $queryOptions = null): Collection|ProblemDetail
    {
        return $this->listResource(
            modelClass: ContactBankAccount::class,
            path: self::PATH,
            filters: $filters,
            queryOptions: $queryOptions,
        );
    }
}
