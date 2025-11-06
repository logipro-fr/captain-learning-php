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
        return new CaptainLearningClient('cl_apk_123', 'sk_example_secret', new MockHttpClient($callable));
    }
    /**
     * @param array<mixed, mixed> $options
     */
    private function callableResponse(string $method, string $url, array $options): MockResponse
    {
        if ($method == 'POST' && str_ends_with($url, '/v1/formation')) {
            return $this->postV1FormationMockResponse();
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
}
