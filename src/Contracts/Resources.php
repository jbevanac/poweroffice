<?php

namespace Poweroffice\Contracts;

use Poweroffice\PowerofficeSDK;
use Poweroffice\Resources\ClientIntegrationInformationResource;
use Poweroffice\Resources\ContactBankAccountsResource;
use Poweroffice\Resources\EmployeeResource;
use Poweroffice\Resources\OnboardingResource;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Poweroffice\Enum\Method;

interface Resources
{
    public function clientIntegrationInformation(): ClientIntegrationInformationResource;

    public function contactBankAccounts(): ContactBankAccountsResource;

    public function onboarding(): OnboardingResource;

    public function employees(): EmployeeResource;
}
