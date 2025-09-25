<?php

namespace Tests\Integration;

use CaptainLearningPhp\CaptainLearningClient;
use PHPUnit\Framework\TestCase;

class CaptainLearningClientIntegrationTest extends TestCase
{
    public function testApiRealCall(): void
    {
        // Ce test fait de vrais appels HTTP - à utiliser avec prudence

        // Arrange
        $client = new CaptainLearningClient();
        $nameFormation = "CaptainLearningClient integration";
        $codeFormation = "E" . uniqid();
        $filepath = dirname(__DIR__, 2) . '/tests/Resources/exemple.pdf';

        // Act
        $response = $client->createFormation($nameFormation, $codeFormation, $filepath);

        // Assert
        $this->assertInstanceOf(CaptainLearningClient::class, $client);
        $this->assertJson($response);

        $responseData = json_decode($response, true);
        $this->assertIsArray($responseData);

        $this->assertArrayHasKey('success', $responseData);
        $this->assertTrue($responseData['success']);

        $this->assertArrayHasKey('data', $responseData);
        $data = $responseData['data'];
        $this->assertIsArray($data);

        $this->assertArrayHasKey('formationId', $data);
        $formationId = $data['formationId'];
        $this->assertIsString($formationId);
        $this->assertEquals('cl_formation_' . $codeFormation, $formationId);
    }

}
