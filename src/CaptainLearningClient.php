<?php

namespace CaptainLearningPhp;

use CaptainLearningPhp\DTO\Formation\FormationCreateRequest;
use CaptainLearningPhp\DTO\Formation\FormationCreateResponse;
use CaptainLearningPhp\Services\Formation\FormationService;
use CaptainLearningPhp\Services\Token\CreateTokenService;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CaptainLearningClient implements CaptainLearningClientInterface
{
    private FormationService $formationService;
    private HttpClientInterface $httpClient;
    private ApiUrls $apiUrls;
    public string $token;

    public function __construct(
        string $apiKeyId,
        string $apiKey,
        ?string $apiUrls = null,
        ?HttpClientInterface $httpClient = null
    ) {
        $this->httpClient = $httpClient ?? new CurlHttpClient();
        if (is_string($apiUrls)) {
            $this->apiUrls = new ApiUrls($apiUrls);
        } else {
            $this->apiUrls = new ApiUrls();
        }
        $this->token = $this->requestToken($apiKeyId, $apiKey);
        $this->formationService = new FormationService($this->httpClient, $this->apiUrls, $this->token);
    }

    private function requestToken(string $apiKeyId, string $apiKey): string
    {
        $createTokenService = new CreateTokenService($this->httpClient, $this->apiUrls);
        $response = $createTokenService->execute($apiKeyId, $apiKey);
        return $response;
    }

    public function createFormation(FormationCreateRequest $formation): FormationCreateResponse
    {
        return $this->formationService->create($formation);
    }
}
