<?php /** @noinspection SpellCheckingInspection */

namespace Poweroffice\Model;

use Poweroffice\Contracts\ModelInterface;

final class Employee implements ModelInterface
{
    use ModelTrait;

    public function __construct(
        public ?int $id = null,
        public ?string $dateOfBirth = null,
        public ?string $departmentCode = null,
        public ?int $departmentId = null,
        public ?string $emailAddress = null,
        public ?string $endDate = null,
        public ?string $externalImportReference = null,
        public ?int $externalNumber = null,
        public ?string $firstName = null,
        public ?string $gender = null,
        public ?string $hiredDate = null,
        public ?string $internationalIdCountryCode = null,
        public ?string $internationalIdNumber = null,
        public ?bool $internationalIdReportToAltinn = null,
        public ?string $internationalIdType = null,
        public ?bool $isArchived = null,
        public ?string $jobTitle = null,
        public ?string $lastName = null,
        public ?string $locationCode = null,
        public ?int $locationId = null,
        public ?MailAddress $mailAddress = null,
        public ?int $managerEmployeeNo = null,
        public ?string $nationalIdNumber = null,
        public ?int $number = null,
        public ?string $phoneNumber = null,
        public ?int $salaryBankAccountId = null,
        public ?string $startDate = null,
        public ?int $subledgerAccountId = null,
        public ?int $travelExpenseBankAccountId = null,
    ) {
    }
}