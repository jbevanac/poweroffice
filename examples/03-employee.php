<?php

use Poweroffice\Model\Employee;
use Poweroffice\Model\ProblemDetail;
use Poweroffice\Plugins\UserAgentPlugin;
use Poweroffice\PowerofficeSDK;
use Poweroffice\Query\Filters\ContactIdsFilter;
use Poweroffice\Query\Filters\EmployeeEmailsFilter;
use Poweroffice\Query\Filters\EmployeeNoFilter;
use Poweroffice\Query\Options\OrderBy;
use Poweroffice\Query\Options\QueryOptions;
use Ramsey\Collection\Collection;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;

require '00-setup.php';

// PSR-6 cache (FilesystemAdapter)
$psr6Cache = new FilesystemAdapter(
    namespace: 'poweroffice',
    defaultLifetime: 3600,
    directory: CACHE_DIR
);

// PSR-16 wrapper
$cache = new Psr16Cache($psr6Cache);

$sdk = new PowerofficeSDK(
    baseUrl: URL,
    applicationKey: APPLICATION_KEY,
    subscriptionKey: SUBSCRIPTION_KEY,
    clientKey: CLIENT_KEY,
    cache: $cache,
    plugins: [new UserAgentPlugin('jbevanac/poweroffice '.VERSION)],
);

$employees = listEmployees($sdk);

$contactIds = [];
foreach ($employees as $employee){
    $contactIds[] = $employee->id;
}
$bankAccounts = $sdk->contactBankAccounts()->list(
    filters: [new ContactIdsFilter($contactIds)],
    queryOptions: new QueryOptions(
        fields: ['id'],
        orderBy: [new OrderBy('contactId')],
    ),
);

dd($bankAccounts);

/**
 * CREATE EMPLOYEE
 */
function createEmployee(PowerofficeSDK $sdk): ProblemDetail|Employee
{
    return $sdk->employees()->create([
        'firstName' => 'Ola',
        'lastName' => 'Nordmann',
        'emailAddress' => null
    ]);
}

function listEmployees(PowerofficeSDK $sdk): ProblemDetail|Collection
{
    $filters = [
        new EmployeeNoFilter([5,6]),
        new EmployeeEmailsFilter(['olanordmann@example.com'])
    ];
    $queryOptions = new QueryOptions(fields: ['id']);

    $filters = [];
    $queryOptions = null;

    return $sdk->employees()->list($filters, $queryOptions);
}

function listBankAccounts(PowerOfficeSDK $sdk): ProblemDetail|Collection
{
    return $sdk->contactBankAccounts()->list();
}
