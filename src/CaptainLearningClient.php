<?php

namespace CaptainLearningPhp;

use CaptainLearningPhp\DTO\Formation\FormationCreateDTO;
use CaptainLearningPhp\Services\FormationService;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CaptainLearningClient implements CaptainLearningClientInterface
{
    private FormationService $formationService;

    public function __construct(
        private HttpClientInterface $httpClient = new CurlHttpClient(),
        protected ApiUrls $apiUrls = new ApiUrls()
    ) {
        $this->formationService = new FormationService($this->httpClient, $this->apiUrls);
    }

    public function createFormation(FormationCreateDTO $formation): string
    {
        return $this->formationService->create($formation);
    }

    // public function createFormation(string $name, string $code, ?string $filePath = null): string
    // {
    //     $body = [
    //         'intituleFormation' => $name,
    //         'code' => $code
    //     ];
    //     if ($filePath !== null && file_exists($filePath)) {
    //         $body['document'] = fopen($filePath, 'r');
    //     }

    //     $response = $this->httpClient->request('POST', 'http://172.17.0.1:11780/api/external/v1/formation', [
    //         'headers' => [
    //             'Accept' => 'application/json',
    //             'User-Agent' => 'CaptainLearningClient/1.0'
    //         ],
    //         'body' => $body
    //     ]);

    //     return $response->getContent(false);
    // }
}
