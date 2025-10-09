<?php

namespace Tests\Unit\Services;

use CaptainLearningPhp\ApiUrls;
use CaptainLearningPhp\DTO\Formation\FormationCreateDTO;
use CaptainLearningPhp\Services\FormationService;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class FormationServiceTest extends TestCase
{
    public function testCreateFormationRequiresIntituleAndCode(): void
    {
        $httpClientMock = $this->getMockBuilder(HttpClientInterface::class)
        ->onlyMethods(['request','stream'])
        ->getMock();

        $apiUrlsMock = $this->getMockBuilder(\CaptainLearningPhp\ApiUrls::class)
        ->disableOriginalConstructor()
        ->getMock();

    // On vérifie que le body envoyé contient bien les deux clés
        $httpClientMock->expects($this->once())
        ->method('request')
        ->with(
            $this->equalTo('POST'),
            $this->anything(),
            $this->callback(function ($options) {
                if (!is_array($options)) {
                    return false;
                }
                $body = $options['body'] ?? [];
                return is_array($body)
                && isset($body['intituleFormation'])
                && isset($body['code']);
            })
        )
        ->willReturn($this->createMock(ResponseInterface::class));

        $service = new FormationService($httpClientMock, $apiUrlsMock);

        $dto = new FormationCreateDTO('Titre formation', 'CODE123');
        $service->create($dto);
    }

    public function testCreateFormationReturnsRawContentEvenOnError(): void
    {
        $httpClientMock = $this->getMockBuilder(HttpClientInterface::class)
            ->onlyMethods(['request','stream'])
            ->getMock();

        $apiUrlsMock = $this->getMockBuilder(ApiUrls::class)
            ->disableOriginalConstructor()
            ->getMock();

        $responseMock = $this->getMockBuilder(ResponseInterface::class)
            ->onlyMethods([
                'getContent',
                'getStatusCode',
                'getHeaders',
                'toArray',
                'cancel',
                'getInfo'
            ])
            ->getMock();
        // Simule une réponse avec un code d'erreur
        $responseMock->expects($this->once())
            ->method('getContent')
            ->with(false)
            ->willReturn('error content');

        $httpClientMock->expects($this->once())
            ->method('request')
            ->willReturn($responseMock);

        $service = new FormationService($httpClientMock, $apiUrlsMock);

        $dto = new FormationCreateDTO('Titre', 'CODE123');
        $result = $service->create($dto);

        $this->assertEquals('error content', $result);
    }

    public function testCreateFormationSendsAcceptHeader(): void
    {
        $httpClientMock = $this->getMockBuilder(HttpClientInterface::class)
        ->onlyMethods(['request', 'stream'])
        ->getMock();

        $apiUrlsMock = $this->getMockBuilder(ApiUrls::class)
        ->disableOriginalConstructor()
        ->getMock();

        $httpClientMock->expects($this->once())
        ->method('request')
        ->with(
            $this->equalTo('POST'),
            $this->anything(),
            $this->callback(function ($options) {
                if (!is_array($options)) {
                    return false;
                }
                $headers = $options['headers'] ?? [];
                if (!is_array($headers)) {
                    return false;
                }
                return isset($headers['Accept'])
                && $headers['Accept'] === 'application/json';
            })
        )
        ->willReturn($this->createMock(ResponseInterface::class));

        $service = new FormationService($httpClientMock, $apiUrlsMock);
        $dto = new FormationCreateDTO('Titre', 'CODE123');
        $service->create($dto);
    }
}
