<?php

namespace Tests\Unit;

use CaptainLearningPhp\CaptainLearningClient;
use CaptainLearningPhp\CaptainLearningClientFactory;
use CaptainLearningPhp\DTO\Formation\FormationCreateDTO;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

use function Safe\json_decode;
use function Safe\json_encode;

class CaptainLearningClientTest extends TestCase
{
    private CaptainLearningClient $client;

    public function setUp(): void
    {
        $captainLearningClientFactory = new CaptainLearningClientFactory();
        $this->client = $captainLearningClientFactory->createMockCaptainLearning();
    }
    public function testCreateFormation(): void
    {
        // Arrange
        $getFile = new GetFile();
        $file = $getFile->buildUploadedFile();
        $nameFormation = "Formation de test";
        $codeFormation = "123";
        $formation = new FormationCreateDTO($nameFormation, $codeFormation, $file);


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
        $this->assertEquals('cl_formation_' . $codeFormation, $formationId);
    }

    // public function testCreateFormation(): void
    // {
    //     // Arrange
    //     $nameFormation = "Formation de test";
    //     $codeFormation = "E100-2509-00002";

    //     $responseData = [
    //         "success" => true,
    //         "data" => ["formationId" => "cl_formation_" . $codeFormation],
    //         "error" => "",
    //         "error_message" => ""
    //         ];
    //     $expectedResponse = json_encode($responseData);

    //     $mockResponse = new MockResponse($expectedResponse, [
    //         'http_code' => 200,
    //         'response_headers' => ['Content-Type' => 'application/json']
    //     ]);
    //     $mockhttp = new MockHttpClient([$mockResponse]);

    //     // Act
    //     $client = new CaptainLearningClient($mockhttp);
    //     $response = $client->createFormation($nameFormation, $codeFormation);
    //     // Assert

    //     $this->assertInstanceOf(CaptainLearningClient::class, $client);
    //     $this->assertJson($response);

    //     /** @var array<string, mixed>   */
    //     $responseData = json_decode($response, true);
    //     $this->assertTrue(isset($responseData['success']));
    //     $data = $responseData['data'];
    //     $this->assertIsArray($data);
    //     /** @var string $formationId     */
    //     $formationId = $data['formationId'];
    //     $this->assertEquals('cl_formation_' . $codeFormation, $formationId);
    // }
    // public function testCreateFormationFail(): void
    // {
    //     // Arrange
    //     $nameFormation = "Formation de test";
    //     $codeFormation = "E100-2509-00002";

    //     $responseData = [
    //         'success' => false,
    //         'data' => null,
    //         'error' => 'Doctrine\\DBAL\\Exception\\UniqueConstraintViolationException',
    //         'error_message' => 'An exception occurred while executing a query: SQLSTATE[23000]:
    //              Integrity constraint violation: 19 UNIQUE constraint failed: formations.formation_id'
    //     ];

    //     $expectedResponse = json_encode($responseData);
    //     $mockResponse = new MockResponse($expectedResponse, [
    //         'http_code' => 400,
    //         'response_headers' => ['Content-Type' => 'application/json']
    //     ]);
    //     $mockhttp = new MockHttpClient([$mockResponse]);

    //     // Act
    //     $client = new CaptainLearningClient($mockhttp);
    //     $response = $client->createFormation($nameFormation, $codeFormation);

    //     // Assert
    //     $this->assertInstanceOf(CaptainLearningClient::class, $client);
    //     $this->assertJson($response);

    //     /** @var array<string, mixed>   */
    //     $responseData = json_decode($response, true);
    //     $this->assertTrue(isset($responseData['success']));
    //     $this->assertFalse($responseData['success']);
    //     $this->assertEquals("Doctrine\\DBAL\\Exception\\UniqueConstraintViolationException", $responseData['error']);
    // }
}
