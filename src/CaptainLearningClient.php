<?php

namespace CaptainLearningPhp;

use CaptainLearningPhp\DTO\Formation\FormationCreateRequest;
use CaptainLearningPhp\DTO\Formation\FormationCreateResponse;
use CaptainLearningPhp\Services\Formation\FormationService;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CaptainLearningClient implements CaptainLearningClientInterface
{
    private FormationService $formationService;
    private HttpClientInterface $httpClient;
    private ApiUrls $apiUrls;

    public function __construct(
        ?HttpClientInterface $httpClient = null,
        ?ApiUrls $apiUrls = null
    ) {
        $this->httpClient = $httpClient ?? new CurlHttpClient();
        $this->apiUrls = $apiUrls ?? new  ApiUrls();
        $this->formationService = new FormationService($this->httpClient, $this->apiUrls);
    }

    public function createFormation(FormationCreateRequest $formation): FormationCreateResponse
    {
        return $this->formationService->create($formation);
    }
}
