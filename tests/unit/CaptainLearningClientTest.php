<?php

namespace Tests\Unit;

use CaptainLearningPhp\ApiUrls;
use CaptainLearningPhp\CaptainLearningClient;
use CaptainLearningPhp\CaptainLearningClientFactory;
use CaptainLearningPhp\DTO\Formation\FormationCreateDTO;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function Safe\json_decode;

class CaptainLearningClientTest extends TestCase
{
    protected CaptainLearningClient $client;
    /** @var non-empty-string */
    protected string $codeFormation;

    protected function setUp(): void
    {
        $captainLearningClientFactory = new CaptainLearningClientFactory();
        $this->client = $captainLearningClientFactory->createMockCaptainLearning();
        $this->codeFormation = "123";
    }
    public function testCreateFormation(): void
    {
        // Arrange
        $getFile = new GetFile();
        $file = $getFile->buildUploadedFile();
        $nameFormation = "Formation de test";
        $formation = new FormationCreateDTO($nameFormation, $this->codeFormation, $file);


        // Act
        $response = $this->client->createFormation($formation);

        // Assert
        /** @var array<string, mixed>   */
        $responseData = json_decode($response, true);
        $this->assertTrue(isset($responseData['success']));
        $data = $responseData['data'];
        $this->assertIsArray($data);
        /** @var string $formationId     */
        $formationId = $data['formationId'];
        $this->assertStringStartsWith('cl_formation_', $formationId);
        $this->assertNotEmpty($this->codeFormation);
        $this->assertStringEndsWith($this->codeFormation, $formationId);
    }

    public function testCreateFormationWithoutFile(): void
    {
        // Arrange
        $nameFormation = "Formation de test sans fichier";
        $formation = new FormationCreateDTO($nameFormation, $this->codeFormation);

        // Act
        $response = $this->client->createFormation($formation);

        // Assert
        /** @var array<string, mixed>   */
        $responseData = json_decode($response, true);
        $this->assertTrue(isset($responseData['success']));
        $data = $responseData['data'];
        $this->assertIsArray($data);
        /** @var string $formationId     */
        $formationId = $data['formationId'];
        $this->assertStringStartsWith('cl_formation_', $formationId);
        $this->assertNotEmpty($this->codeFormation);
        $this->assertStringEndsWith($this->codeFormation, $formationId);
    }

    public function testConstructorUsesProvidedApiUrls(): void
    {
        $httpClientMock = $this->getMockBuilder(HttpClientInterface::class)
            ->onlyMethods(['request','stream'])
            ->getMock();

        $customApiUrls = $this->getMockBuilder(ApiUrls::class)
            ->disableOriginalConstructor()
            ->getMock();

        $client = new CaptainLearningClient($httpClientMock, $customApiUrls);

        // Utilise Reflection pour accéder à la propriété privée
        $reflection = new \ReflectionClass($client);
        $property = $reflection->getProperty('apiUrls');
        $property->setAccessible(true);

        $this->assertSame($customApiUrls, $property->getValue($client));
    }
}
