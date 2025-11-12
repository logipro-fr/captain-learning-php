<?php

namespace Tests\Unit\Services\Formation;

use CaptainLearningPhp\ApiUrls;
use CaptainLearningPhp\DTO\Formation\FormationCreateRequest;
use CaptainLearningPhp\DTO\Formation\FormationCreateResponse;
use CaptainLearningPhp\Exceptions\Formation\FormationBadRequestException;
use CaptainLearningPhp\Services\Formation\FormationService;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class FormationServiceTest extends TestCase
{
    public function testCreateFormation(): void
    {
     // Arrange
        $apiUrls = new ApiUrls();
        $responseExpected = new FormationCreateResponse(true, ['formationId' => 'cl_formation_123'], '', '');
        $jsonResponse = json_encode($responseExpected);

        $httpClientMock = $this->getMockBuilder(HttpClientInterface::class)
            ->onlyMethods(['request', 'stream'])
            ->getMock();

        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getContent')
        ->with(false)
        ->willReturn($jsonResponse);

        $httpClientMock->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo('POST'),
                $this->equalTo($apiUrls->createFormation()),
            )
            ->willReturn($responseMock);

        $sut = new FormationService(
            $httpClientMock,
            $apiUrls,
            'sk_test_token'
        );
        // Act
        $formationCreateDTO = new FormationCreateRequest('Test Formation', 'CODE123');

        $result = $sut->create($formationCreateDTO);

        // Assert
        $this->assertInstanceOf(FormationCreateResponse::class, $result);
        $this->assertTrue($result->success);
        $this->assertNotNull($result->data);
        $this->assertArrayHasKey('formationId', $result->data);
        $this->assertEquals('cl_formation_123', $result->data['formationId']);
        $this->assertEquals('', $result->error);
        $this->assertEquals('', $result->error_message);
    }

    public function testFailCreateFormation(): void
    {
     // Arrange
        $apiUrls = new ApiUrls();
        $responseExpected = new FormationCreateResponse(false, [], 'ERR001', 'Invalid data');
        $jsonResponse = json_encode($responseExpected);

        $httpClientMock = $this->getMockBuilder(HttpClientInterface::class)
            ->onlyMethods(['request', 'stream'])
            ->getMock();

        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getContent')
        ->with(false)
        ->willReturn($jsonResponse);

        $httpClientMock->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo('POST'),
                $this->equalTo($apiUrls->createFormation()),
            )
            ->willReturn($responseMock);

        $sut = new FormationService(
            $httpClientMock,
            $apiUrls,
            'sk_test_token'
        );
        // Act

        $this->expectException(FormationBadRequestException::class);
        $this->expectExceptionMessage('Bad request exception with content ');

        $formationCreateDTO = new FormationCreateRequest('', '');
        $sut->create($formationCreateDTO);
    }

    public function testFailRequest(): void
    {
        // Arrange
        $apiUrls = new ApiUrls();

        /** @var HttpClientInterface&\PHPUnit\Framework\MockObject\MockObject $httpClientMock */
        $httpClientMock = $this->getMockBuilder(HttpClientInterface::class)
            ->onlyMethods(['request', 'stream'])
            ->getMock();

        // Le mock lève une exception quand on appelle request()
        $httpClientMock->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo('POST'),
                $this->equalTo($apiUrls->createFormation())
            )
            ->willThrowException(new FormationBadRequestException('Invalid formation data'));


        $sut = new FormationService($httpClientMock, $apiUrls, 'sk_test_token');

        // Act & Assert
        $this->expectException(FormationBadRequestException::class);
        $this->expectExceptionMessage('Bad request exception with content ');

        $formationCreateDTO = new FormationCreateRequest('Test Formation', 'CODE123');
        $sut->create($formationCreateDTO);
    }
}
