<?php

namespace CaptainLearningPhp\Services\Formation;

use CaptainLearningPhp\ApiUrls;
use CaptainLearningPhp\DTO\Formation\FormationCreateRequest;
use CaptainLearningPhp\DTO\Formation\FormationCreateResponse;
use CaptainLearningPhp\Exceptions\FormationBadRequestException;
use Exception;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class FormationService
{
    private HttpClientInterface $httpClient;
    private ApiUrls $apiUrls;

    public function __construct(
        HttpClientInterface $httpClient,
        ApiUrls $apiUrls
    ) {
        $this->httpClient = $httpClient;
        $this->apiUrls = $apiUrls;
    }

    public function create(FormationCreateRequest $formation): FormationCreateResponse
    {
        $body = [
        'intituleFormation' => $formation->intituleFormation,
        'code' => $formation->code,
        ];

        if ($formation->file !== null) {
            $body['document'] = DataPart::fromPath(
                $formation->file->getPathname(),
                $formation->file->getClientOriginalName(),
                $formation->file->getMimeType()
            );
        }

        $formData = new FormDataPart($body);

        try {
            $response = $this->httpClient->request('POST', $this->apiUrls->createFormation(), [
                'headers' => [
                    'Accept' => 'application/json',
                    'User-Agent' => 'CaptainLearningClient/1.0'
                ] + $formData->getPreparedHeaders()->toArray(),
                'body' => $formData->bodyToIterable()
            ]);
        } catch (Exception $e) {
            throw new FormationBadRequestException('Network error: ' . $e->getMessage());
        }

        $result = $this->getFormationCreateResponse($response);

        if (!$result->success) {
            throw new FormationBadRequestException($result->error_message);
        }

        return $result;
    }


    private function getFormationCreateResponse(ResponseInterface $response): FormationCreateResponse
    {
        /**
         * @var array{
         *     success: bool,
         *     data: array<string, mixed> | null,
         *     error: string,
         *     error_message: string
         * } $responseData
         */
        $responseData = json_decode($response->getContent(false), true);
        $result = new FormationCreateResponse(
            $responseData['success'],
            $responseData['data'],
            $responseData['error'],
            $responseData['error_message']
        );
        return $result;
    }
}
