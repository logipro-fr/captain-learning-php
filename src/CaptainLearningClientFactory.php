<?php

namespace CaptainLearningPhp;

use CaptainLearningPhp\BaseDir;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

use function Safe\file_get_contents;

class CaptainLearningClientFactory
{
    private const RESPONSE_JSON_PATH = '/src/ResponseJSON';

    public function createMockCaptainLearning(): CaptainLearningClient
    {
        $callable = function (string $method, string $url, array $options): MockResponse {
            return $this->callableResponse($method, $url, $options);
        };
        return new CaptainLearningClient('cl_apk_123', 'sk_example_secret', null, null, new MockHttpClient($callable));
    }
    /**
     * @param array<mixed, mixed> $options
     */
    private function callableResponse(string $method, string $url, array $options): MockResponse
    {
        if ($method == 'POST' && str_ends_with($url, '/v1/formation')) {
            return $this->postV1FormationMockResponse();
        }
        if ($method == 'PATCH' && str_contains($url, '/v1/formation')) {
            $urlParts = explode('/', $url);
            $formationCode = end($urlParts);
            if (!empty($formationCode) && $formationCode !== 'invalid_code') {
                return $this->updateV1FormationMockResponse();
            } else {
                return $this->updateV1FormationNotFoundMockResponse();
            }
        }
        if ($method == 'POST' && str_ends_with($url, '/v1/token')) {
            return $this->postV1TokenMockResponse();
        }
        throw new BadRequestException();
    }

    private function readResponseJson(string $relativePath): string
    {
        $path = BaseDir::getFullPath(self::RESPONSE_JSON_PATH . $relativePath);
        $content = file_get_contents($path);
        return $content;
    }

    private function postV1FormationMockResponse(): MockResponse
    {
        $response = $this->readResponseJson('/Formation/postFormation.json');
        return new MockResponse($response);
    }
    private function postV1TokenMockResponse(): MockResponse
    {
        $response = $this->readResponseJson('/Token/createToken.json');
        return new MockResponse($response);
    }
    private function updateV1FormationMockResponse(): MockResponse
    {
        $response = $this->readResponseJson('/Formation/updateFormation.json');
        return new MockResponse($response);
    }
    private function updateV1FormationNotFoundMockResponse(): MockResponse
    {
        $response = $this->readResponseJson('/Formation/failUpdateFormation.json');

        // $response = '{
        //     "success": false,
        //     "data": null,
        //     "error": "CaptainLearning\Domain\Model\Formation\Exceptions\FormationNotFoundException",
        //     "error_message": "Formation with Id invalid_code not found"
        // }';
        return new MockResponse($response);
    }
}
