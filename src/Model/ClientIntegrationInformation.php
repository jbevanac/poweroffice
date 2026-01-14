<?php

namespace Poweroffice\Model;

use Poweroffice\Contracts\ModelInterface;

final class ClientIntegrationInformation implements ModelInterface
{
    use ModelTrait;

    public function __construct(
        public ?array $activeClientSubscriptions = null,
        public ?string $clientId = null,
        public ?string $clientName = null,
        public ?array $invalidPrivileges = null,
        public ?array $validPrivileges = null,
    ) {
    }

    public function hasPrivilege(string $privilege): bool
    {
        return !empty($this->validPrivileges) && in_array($privilege, $this->validPrivileges);
    }
}
