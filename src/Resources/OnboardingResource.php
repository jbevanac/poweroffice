<?php

namespace Poweroffice\Resources;

use Poweroffice\Contracts\ResourceInterface;
use Poweroffice\Enum\Method;
use Poweroffice\Enum\Status;
use Poweroffice\Exceptions\FailedToDecodeJsonResponseException;
use Poweroffice\Exceptions\FailedToSendRequestException;
use Poweroffice\Exceptions\SerializerException;
use Poweroffice\Exceptions\UriTooLongException;
use Poweroffice\Model\FinalizeOnboardingResponse;
use Poweroffice\Model\InitiateOnboardingResponse;
use Poweroffice\Model\ProblemDetail;
use Poweroffice\Resources\Concerns\CanAccessSDK;
use Poweroffice\Resources\Concerns\CanCreateRequest;

final class OnboardingResource implements ResourceInterface
{
    private const string PATH = 'Onboarding';

    use CanAccessSDK;
    use CanCreateRequest;


    /**
     * @throws FailedToDecodeJsonResponseException
     * @throws FailedToSendRequestException
     * @throws UriTooLongException
     * @throws SerializerException
     */
    public function initiate(string $clientOrganizationNo, string $redirectUri): ProblemDetail|InitiateOnboardingResponse
    {
        $body = [
            'ApplicationKey' => $this->getSdk()->getApplicationKey(),
            'clientOrganizationNo' => $clientOrganizationNo,
            'redirectUri' => $redirectUri,
        ];

        $request = $this->request(
            method: Method::POST,
            url: [self::PATH, 'Initiate'],
            body: json_encode($body),
        );

        $response = $this->sendRequest($request, false);
        $data = $this->decodeJsonResponse($response);

        if (Status::OK->value === $response->getStatusCode()) {
            /** @var InitiateOnboardingResponse $IOR */
            $IOR = InitiateOnboardingResponse::make($data);
            return $IOR;
        }

        /** @var ProblemDetail $problemDetail */
        $problemDetail = ProblemDetail::make($data);

        return $problemDetail;
    }

    /**
     * @throws FailedToDecodeJsonResponseException
     * @throws UriTooLongException
     * @throws SerializerException
     * @throws FailedToSendRequestException
     */
    public function finalize(string $onboardingToken): ProblemDetail|FinalizeOnboardingResponse
    {
        $body = [
            'OnboardingToken' => $onboardingToken
        ];

        $request = $this->request(
            method: Method::POST,
            url: [self::PATH, 'Finalize'],
            body: json_encode($body),
        );

        $response = $this->sendRequest($request, false);
        $data = $this->decodeJsonResponse($response);

        if (Status::OK->value === $response->getStatusCode()) {
            /** @var FinalizeOnboardingResponse $FOR */
            $FOR = FinalizeOnboardingResponse::make($data);
            return $FOR;
        }

        /** @var ProblemDetail $problemDetail */
        $problemDetail = ProblemDetail::make($data);

        return $problemDetail;
    }
}
