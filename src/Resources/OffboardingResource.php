<?php

namespace Poweroffice\Resources;

use Poweroffice\Contracts\ResourceInterface;
use Poweroffice\Enum\Method;
use Poweroffice\Enum\Status;
use Poweroffice\Exceptions\FailedToDecodeJsonResponseException;
use Poweroffice\Exceptions\FailedToSendRequestException;
use Poweroffice\Exceptions\SerializerException;
use Poweroffice\Exceptions\UriTooLongException;
use Poweroffice\Model\OffboardingDTO;
use Poweroffice\Model\ProblemDetail;
use Poweroffice\Resources\Concerns\CanAccessSDK;
use Poweroffice\Resources\Concerns\CanCreateRequest;

final class OffboardingResource implements ResourceInterface
{
    private const string PATH = 'Offboarding';

    use CanAccessSDK;
    use CanCreateRequest;

    /**
     * @throws FailedToDecodeJsonResponseException
     * @throws FailedToSendRequestException
     * @throws UriTooLongException
     * @throws SerializerException
     */
    public function removeIntegration(): ProblemDetail|OffboardingDTO
    {
        $request = $this->request(
            method: Method::DELETE,
            url: [self::PATH, 'RemoveIntegration'],
        );

        $response = $this->sendRequest($request);
        $data = $this->decodeJsonResponse($response);

        if (Status::OK->value === $response->getStatusCode()) {
            /** @var OffboardingDTO $OR */
            $OR = OffboardingDTO::make($data);
            return $OR;
        }

        /** @var ProblemDetail $problemDetail */
        $problemDetail = ProblemDetail::make($data);

        return $problemDetail;
    }
}
