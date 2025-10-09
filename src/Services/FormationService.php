<?php

namespace CaptainLearningPhp\Services;

use CaptainLearningPhp\ApiUrls;
use CaptainLearningPhp\DTO\Formation\FormationCreateDTO;
use Symfony\Contracts\HttpClient\HttpClientInterface;

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

    public function create(FormationCreateDTO $formation): string
    {

        $body = [
            'intituleFormation' => $formation->intituleFormation,
            'code' => $formation->code,
            'document' => $formation->file !== null ? fopen($formation->file->getPathname(), 'r') : null,
        ];

        $response = $this->httpClient->request('POST', $this->apiUrls->createFormation(), [
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => 'CaptainLearningClient/1.0'
            ],
            'body' => $body
        ]);

        return $response->getContent(false);
    }
}
