<?php

namespace Poweroffice\Model;

use Poweroffice\Contracts\ModelInterface;

final class EmployeeBankAccounts implements ModelInterface
{
    use ModelTrait;

    public function __construct(
        public ?int $expensesBankAccountId = null,
        public ?int $salaryBankAccountId = null,
        public ?int $travelExpenseBankAccountId = null,
    ) {
    }
}
