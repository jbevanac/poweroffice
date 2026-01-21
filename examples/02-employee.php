<?php

use Poweroffice\PowerofficeSDK;
use Poweroffice\Query\Filters\EmployeeEmailsFilter;
use Poweroffice\Query\Filters\EmployeeNoFilter;
use Poweroffice\Query\Options\QueryOptions;

require '00-setup.php';

$sdk = new PowerofficeSDK(
    baseUrl: URL,
    applicationKey: APPLICATION_KEY,
    clientKey: CLIENT_KEY,
    subscriptionKey: SUBSCRIPTION_KEY
);
listBankAccounts($sdk);
// listEmployees($sdk);
/**
 * CREATE EMPLOYEE
 */
function createEmployee(PowerofficeSDK $sdk)
{
    $employee = $sdk->employees()->create([
        'firstName' => 'Ola',
        'lastName' => 'Nordmann',
        'emailAddress' => null
    ]);
    dump($employee);
}

function listEmployees(PowerofficeSDK $sdk)
{
    $filters = [
        new EmployeeNoFilter([5,6]),
        new EmployeeEmailsFilter(['olanordmann@example.com'])
    ];
    $queryOptions = new QueryOptions(fields: ['id']);

    $filters = [];
    $queryOptions = null;

    $employees = $sdk->employees()->list($filters, $queryOptions);
    dump($employees);
}

function listBankAccounts(PowerOfficeSDK $sdk)
{
    $bankAccounts = $sdk->contactBankAccounts()->list();
    dump($bankAccounts);
}
