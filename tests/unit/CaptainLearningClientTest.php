<?php

namespace Tests\Unit;

use CaptainLearningPhp\ApiUrls;
use CaptainLearningPhp\CaptainLearningClient;
use CaptainLearningPhp\CaptainLearningClientFactory;
use CaptainLearningPhp\DTO\Formation\FormationCreateRequest;
use CaptainLearningPhp\DTO\Formation\FormationGetRequest;
use CaptainLearningPhp\DTO\Formation\FormationResponse;
use CaptainLearningPhp\DTO\Formation\FormationUpdateRequest;
use CaptainLearningPhp\Exceptions\Formation\FormationBadRequestException;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class CaptainLearningClientTest extends TestCase
{
    protected CaptainLearningClient $client;
    /** @var non-empty-string */
    protected string $codeFormation;
    protected string $apiKey;
    protected string $apiKeyId;
    protected string $apiUrls;
    protected string $tenantId;


    protected function setUp(): void
    {
        $this->codeFormation = "123";
        $this->apiKeyId = 'cl_apk_123';
        $this->apiKey = 'sk_example_secret';
        $this->tenantId = 'demo_tenant';
        $this->apiUrls = 'https://api.captainlearning.com';
        $captainLearningClientFactory = new CaptainLearningClientFactory();
        $this->client = $captainLearningClientFactory->createMockCaptainLearning();
    }

    public function testCreateFormation(): void
    {
        // Arrange
        $getFile = new GetFile();
        $file = $getFile->buildUploadedFile();
        $nameFormation = "Formation de test avec tenant_id";
        $formation = new FormationCreateRequest($nameFormation, $this->codeFormation, $file);

        // Act
        $responseCreateDTO = $this->client->createFormation($formation);

        // Assert
        $this->assertInstanceOf(FormationResponse::class, $responseCreateDTO);
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
        $this->assertInstanceOf(FormationResponse::class, $responseCreateDTO);
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


        $client = new CaptainLearningClient(
            $this->apiKeyId,
            $this->apiKey,
            $this->apiUrls,
            $this->tenantId,
            $httpClientMock
        );

        // Utilise Reflection pour accéder à la propriété privée
        $reflection = new \ReflectionClass($client);
        $property = $reflection->getProperty('apiUrls');
        $property->setAccessible(true);

        $this->assertEquals(new ApiUrls($this->apiUrls, $this->tenantId), $property->getValue($client));
    }

    public function testUpdateFormation(): void
    {
        //arrange
        $nameFormation = "Formation de test sans fichier";
        $code = $this->codeFormation;
        $formation = new FormationCreateRequest($nameFormation, $code);

        $this->client->createFormation($formation);

        $newNameFormation = "Formation de test update";
        $formationUpdate = new FormationUpdateRequest($newNameFormation, $code);

        //act
        $responseUpdateDTO = $this->client->updateFormation($formationUpdate);

        //assert
        $this->assertInstanceOf(FormationResponse::class, $responseUpdateDTO);
        $this->assertTrue($responseUpdateDTO->success);
        $this->assertTrue($responseUpdateDTO->data !== null);
    }

    public function testFailUpdateFormation(): void
    {
        //arrange
        $nameFormation = "Formation de test sans fichier";
        $formation = new FormationUpdateRequest($nameFormation, CaptainLearningClientFactory::ID_NOT_FOUND);

        //assert & act
        $this->expectException(FormationBadRequestException::class);
        $this->client->updateFormation($formation);
    }
    public function testUpdateFormationThrowsExceptionOnNetworkError(): void
    {
        // Arrange
        $nameFormation = "Formation de test sans fichier";
        $formation = new FormationUpdateRequest($nameFormation, CaptainLearningClientFactory::ID_NOT_FOUND);

        $client = $this->createMockClientThatThrowsOnRequest('PATCH');
        // Assert & Act
        $this->expectException(FormationBadRequestException::class);
        // $this->expectExceptionMessage('Network error:');

        $client->updateFormation($formation);
    }
    public function testGetFormation(): void
    {
        //arrange
        $codeFormation = $this->codeFormation;
        //act
        $formationGetRequest = new FormationGetRequest($codeFormation);
        $responseDTO = $this->client->getFormation($formationGetRequest);
        //assert
        $this->assertInstanceOf(FormationResponse::class, $responseDTO);
        $this->assertTrue($responseDTO->success);
    }

    public function testFailGetFormation(): void
    {
        //arrange
        $codeFormation = CaptainLearningClientFactory::ID_NOT_FOUND;
        //assert & act
        $this->expectException(FormationBadRequestException::class);

        $formationGetRequest = new FormationGetRequest($codeFormation);
        $this->client->getFormation($formationGetRequest);
    }
    public function testGetFormationThrowsExceptionOnNetworkError(): void
    {
        // Arrange

        $client = $this->createMockClientThatThrowsOnRequest('GET');
        // Assert & Act
        $this->expectException(FormationBadRequestException::class);
        $this->expectExceptionMessage('Network error:');
        $formationGetRequest = new FormationGetRequest($this->codeFormation);
        $client->getFormation($formationGetRequest);
    }

    private function createMockClientThatThrowsOnRequest(string $methodRequest): CaptainLearningClient
    {
        $mockClient = new MockHttpClient(function (string $method, string $url, array $options) use ($methodRequest) {
            if ($method === 'POST' && str_ends_with($url, '/v1/token')) {
                return new MockResponse('{
                    "success": true,
                    "data": {
                        "token": "mock.token.value",
                        "apiKeyId": "cl_apk_123"
                    },
                    "error": "",
                    "error_message": ""
                }');
            }
            if ($method === $methodRequest && str_contains($url, '/v1/formation')) {
                throw new class extends \Exception implements TransportExceptionInterface {
                };
            }
            return new MockResponse('{}');
        });
        $client = new CaptainLearningClient(
            'cl_apk_123',
            'sk_example_secret',
            null,
            null,
            $mockClient
        );
        return $client;
    }
}
