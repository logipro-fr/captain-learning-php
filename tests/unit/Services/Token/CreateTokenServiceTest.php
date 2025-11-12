<?php

namespace Tests\Unit\Services\Token;

use CaptainLearningPhp\ApiUrls;
use CaptainLearningPhp\Exceptions\Token\TokenBadRequestException;
use CaptainLearningPhp\Services\Token\CreateTokenService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class CreateTokenServiceTest extends TestCase
{
    public function testCreateToken(): void
    {
        $apiUrls = new ApiUrls();
        $jsonResponse = '{
                "success": true,
                "data": {
                    "token": "eyJ0eXAiOiNiJ9.eyJpYX.1Vz11ZfmW5iq3w",
                    "apiKeyId": "cl_apk_690b25d814d2b"
                },
                "error": "",
                "error_message": ""
            }';

        $mockResponse = new MockResponse($jsonResponse);
        $httpClient = new MockHttpClient($mockResponse);

        $sut = new CreateTokenService($httpClient, $apiUrls);

        $token = $sut->execute('cl_apk_123', 'sk_123456');
        $parts = explode('.', $token);
        $this->assertCount(3, $parts);
    }

    public function testFailureCreateToken(): void
    {
        $apiUrls = new ApiUrls();
        $jsonResponse = '{
                "success": false,
                "data": null,
                "error": "authentication_failed",
                "error_message": "Invalid API credentials"
            }';

        $mockResponse = new MockResponse($jsonResponse);
        $httpClient = new MockHttpClient($mockResponse);

        $sut = new CreateTokenService($httpClient, $apiUrls);

        $this->expectException(TokenBadRequestException::class);
        $this->expectExceptionMessage('Invalid API credentials');
        $sut->execute('cl_apk_invalid', 'sk_invalid');
    }

    public function testFailRequest(): void
    {
        $apiUrls = new ApiUrls();

        $callback = function () {
            throw new \Exception('Network error');
        };

        $httpClient = new MockHttpClient($callback);

        $sut = new CreateTokenService($httpClient, $apiUrls);

        $this->expectException(TokenBadRequestException::class);
        $this->expectExceptionMessage('Network error');
        $sut->execute('cl_apk_123', 'sk_123456');
    }
}
