<?php

namespace Tests\Unit;

use CaptainLearningPhp\ApiUrls;
use CaptainLearningPhp\CaptainLearningClient;
use CaptainLearningPhp\CaptainLearningClientFactory;
use CaptainLearningPhp\DTO\Formation\FormationCreateRequest;
use CaptainLearningPhp\DTO\Formation\FormationCreateResponse;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class CaptainLearningClientTest extends TestCase
{
    protected CaptainLearningClient $client;
    /** @var non-empty-string */
    protected string $codeFormation;
    protected string $apiKey;
    protected string $apiKeyId;


    protected function setUp(): void
    {
        $this->codeFormation = "123";
        $this->apiKeyId = 'cl_apk_123';
        $this->apiKey = 'sk_example_secret';
        $captainLearningClientFactory = new CaptainLearningClientFactory();
        $this->client = $captainLearningClientFactory->createMockCaptainLearning();
    }

    public function testCreateFormation(): void
    {
        // Arrange
        $getFile = new GetFile();
        $file = $getFile->buildUploadedFile();
        $nameFormation = "Formation de test";
        $formation = new FormationCreateRequest($nameFormation, $this->codeFormation, $file);


        // Act
        $responseCreateDTO = $this->client->createFormation($formation);

        // Assert
        $this->assertInstanceOf(FormationCreateResponse::class, $responseCreateDTO);
        $this->assertTrue($responseCreateDTO->success);
        $this->assertTrue($responseCreateDTO->data !== null);

        $this->assertArrayHasKey('formationId', $responseCreateDTO->data);
        $formationID = $responseCreateDTO->data['formationId'];
        $this->assertIsString($formationID);
        $this->assertStringStartsWith('cl_formation_', $formationID);
        $this->assertStringEndsWith($this->codeFormation, $formationID);
        $this->assertEquals('', $responseCreateDTO->error);
        $this->assertEquals('', $responseCreateDTO->error_message);
    }

    public function testCreateFormationWithoutFile(): void
    {
        // Arrange
        $nameFormation = "Formation de test sans fichier";
        $formation = new FormationCreateRequest($nameFormation, $this->codeFormation);

        $responseCreateDTO = $this->client->createFormation($formation);
        // Assert
        $this->assertInstanceOf(FormationCreateResponse::class, $responseCreateDTO);
        $this->assertTrue($responseCreateDTO->success);
        $this->assertTrue($responseCreateDTO->data !== null);
        $this->assertArrayHasKey('formationId', $responseCreateDTO->data);
        $formationID = $responseCreateDTO->data['formationId'];
        $this->assertIsString($formationID);
        $this->assertStringStartsWith('cl_formation_', $formationID);
        $this->assertStringEndsWith($this->codeFormation, $formationID);
        $this->assertEquals('', $responseCreateDTO->error);
        $this->assertEquals('', $responseCreateDTO->error_message);
    }

    public function testConstructorUsesProvidedApiUrls(): void
    {
        $httpClientMock = $this->getMockBuilder(HttpClientInterface::class)
            ->onlyMethods(['request','stream'])
            ->getMock();

        // Ajoute un mock de ResponseInterface
        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getContent')
            ->willReturn('{
                            "success": true,
                            "data": {
                                "token": "mock.token.value",
                                "apiKeyId": "cl_apk_123"
                            },
                            "error": "",
                            "error_message": ""
                        }');

        // Configure le mock pour retourner la réponse
        $httpClientMock->method('request')
            ->willReturn($responseMock);

        $customApiUrls = $this->getMockBuilder(ApiUrls::class)
            ->disableOriginalConstructor()
            ->getMock();

        $client = new CaptainLearningClient($this->apiKeyId, $this->apiKey, $httpClientMock, $customApiUrls);

        // Utilise Reflection pour accéder à la propriété privée
        $reflection = new \ReflectionClass($client);
        $property = $reflection->getProperty('apiUrls');
        $property->setAccessible(true);

        $this->assertSame($customApiUrls, $property->getValue($client));
    }
}
