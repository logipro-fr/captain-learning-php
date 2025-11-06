<?php

namespace Tests\Unit\Services\Token;

use CaptainLearningPhp\ApiUrls;
use CaptainLearningPhp\Exceptions\Token\TokenBadRequestException;
use CaptainLearningPhp\Services\Token\CreateTokenService;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

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

        $httpClientMock = $this->getMockBuilder(HttpClientInterface::class)
            ->onlyMethods(['request', 'stream'])
            ->getMock();

        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getContent')
        ->willReturn($jsonResponse);

        $httpClientMock->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo('POST'),
                $this->equalTo($apiUrls->createToken()),
            )
            ->willReturn($responseMock);

        $sut = new CreateTokenService(
            $httpClientMock,
            $apiUrls
        );

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

        $httpClientMock = $this->getMockBuilder(HttpClientInterface::class)
            ->onlyMethods(['request', 'stream'])
            ->getMock();

        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getContent')
            ->willReturn($jsonResponse);

        $httpClientMock->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo('POST'),
                $this->equalTo($apiUrls->createToken()),
            )
            ->willReturn($responseMock);

        $sut = new CreateTokenService(
            $httpClientMock,
            $apiUrls
        );

        $this->expectException(TokenBadRequestException::class);
        $sut->execute('cl_apk_invalid', 'sk_invalid');
    }
    public function testFailRequest(): void
    {
        $apiUrls = new ApiUrls();

        $httpClientMock = $this->getMockBuilder(HttpClientInterface::class)
           ->onlyMethods(['request', 'stream'])
           ->getMock();

        $httpClientMock->expects($this->once())
           ->method('request')
           ->with(
               $this->equalTo('POST'),
               $this->equalTo($apiUrls->createToken()),
           )
           ->willThrowException(new \Exception('Network error'));

        $sut = new CreateTokenService(
            $httpClientMock,
            $apiUrls
        );

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Network error');
        $sut->execute('cl_apk_123', 'sk_123456');
    }
}
