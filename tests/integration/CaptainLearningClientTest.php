<?php

namespace Tests\Integration;

use CaptainLearningPhp\ApiUrls;
use CaptainLearningPhp\CaptainLearningClient;
use CaptainLearningPhp\DTO\Formation\FormationCreateDTO;
use Tests\Unit\CaptainLearningClientTest as UnitCaptainLearningClientTest;
use Tests\Unit\GetFile;

class CaptainLearningClientTest extends UnitCaptainLearningClientTest
{
    public function testApiRealCall(): void
    {
        // Ce test fait de vrais appels HTTP - à utiliser avec prudence

        // Arrange
        $client = new CaptainLearningClient(
            apiUrls: new ApiUrls('http://172.17.0.1:11780')
        );
        $nameFormation = "CaptainLearningClient integration";
        $codeFormation = "E" . uniqid();
        $getFile = new GetFile();
        $file = $getFile->buildUploadedFile();

        $createFormationDto = new FormationCreateDTO(
            intituleFormation: $nameFormation,
            code: $codeFormation,
            file: $file
        );
        // Act
        $response = $client->createFormation($createFormationDto);

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
