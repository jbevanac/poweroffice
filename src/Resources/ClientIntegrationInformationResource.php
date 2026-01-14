<?php

namespace Poweroffice\Resources;

use Poweroffice\Model\ClientIntegrationInformation;
use Poweroffice\Model\ProblemDetail;
use Poweroffice\Resources\Concerns\CanCreateCollection;
use Poweroffice\Resources\Concerns\CanCreateRequest;
use Poweroffice\Resources\Concerns\CanFindResource;
use Poweroffice\Contracts\ResourceInterface;
use Poweroffice\Exceptions\ApiException;
use Poweroffice\Resources\Concerns\CanAccessSDK;

final class ClientIntegrationInformationResource implements ResourceInterface
{
    use CanAccessSDK;
    use CanCreateRequest;
    use CanCreateCollection;
    use CanFindResource;

    /**
     * @throws ApiException
     */
    public function find(): ClientIntegrationInformation|ProblemDetail
    {
        return $this->findResource(
            modelClass: ClientIntegrationInformation::class,
            path: 'clientIntegrationInformation',
        );
    }
}
