<?php

namespace CaptainLearningPhp\Services\Formation;

use CaptainLearningPhp\ApiUrls;
use CaptainLearningPhp\DTO\Formation\FormationCreateRequest;
use CaptainLearningPhp\DTO\Formation\FormationCreateResponse;
use CaptainLearningPhp\Exceptions\Formation\FormationBadRequestException;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Throwable;

class FormationService
{
    private HttpClientInterface $httpClient;
    private ApiUrls $apiUrls;
    private string $token;

    public function __construct(
        HttpClientInterface $httpClient,
        ApiUrls $apiUrls,
        string $token
    ) {
        $this->httpClient = $httpClient;
        $this->apiUrls = $apiUrls;
        $this->token = $token;
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
                    'User-Agent' => 'CaptainLearningClient/1.0',
                    'Authorization' => 'Bearer ' . $this->token
                ] + $formData->getPreparedHeaders()->toArray(),
                'body' => $formData->bodyToIterable()
            ]);
        } catch (Throwable $e) {
            throw new FormationBadRequestException('Network error: ' . $e->getMessage());
        }

        $result = $this->getFormationCreateResponse($response);

        if (!$result->success) {
            throw new FormationBadRequestException($result->error_message);
        }

        return $result;
    }

    public function update(FormationCreateRequest $formation): FormationCreateResponse
    {
        $content = [
            'intituleFormation' => $formation->intituleFormation,
        ];

        try {
            $response = $this->httpClient->request('PATCH', $this->apiUrls->updateFormation($formation->code), [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'User-Agent' => 'CaptainLearningClient/1.0',
                    'Authorization' => 'Bearer ' . $this->token
                ],
                'json' => $content
            ]);
        } catch (Throwable $e) {
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
