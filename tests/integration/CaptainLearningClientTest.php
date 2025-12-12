<?php

namespace Tests\Integration;

use CaptainLearningPhp\CaptainLearningClient;
use CaptainLearningPhp\DTO\Formation\FormationCreateRequest;
use CaptainLearningPhp\DTO\Formation\FormationGetRequest;
use CaptainLearningPhp\DTO\Formation\FormationResponse;
use CaptainLearningPhp\DTO\Formation\FormationUpdateRequest;
use CaptainLearningPhp\Exceptions\Formation\FormationBadRequestException;
use Tests\Unit\CaptainLearningClientTest as UnitCaptainLearningClientTest;

class CaptainLearningClientTest extends UnitCaptainLearningClientTest
{
    private const PORT = 11780;
    private const HOST = '172.17.0.1';
    private const BASE_URL = 'http://' . self::HOST . ':' . self::PORT;

    public function setUp(): void
    {
        $connection = @fsockopen(self::HOST, self::PORT, $errno, $errstr, 2);
        if (!$connection) {
            $this->markTestSkipped(
                sprintf(
                    'CaptainLearning API is not reachable at %s:%d - %s',
                    self::HOST,
                    self::PORT,
                    $errstr ?: 'Connection failed'
                )
            );
        }
        fclose($connection);


        // dev demo
        // $this->apiKey = "sk_28f9dddd4a00d78c4bdc0f94a6c3f5428bc82dae4d4b7ba920b94c1cad29a6e9";
        // $this->apiKeyId = "cl_apk_6924772ee80be";
        // $this->tenantId = $this->getTenantId('demo');
        // $this->apiUrls = 'https://dev.captain-learning.com';

        // tenant_id = demo
        $this->apiKey = "sk_e7d1a0d1afc0feba66158536fbee1f02ba00a0c993bb4313359c306e958358b1";
        $this->apiKeyId = "cl_apk_693a83860890d";
        $this->tenantId = $this->getTenantId('demo');

        // Sans tenant_id
        // $this->apiKey = "sk_50f27e157605f99ecf721f53372fee62d18dbfc397dcc15236195a63ff47ac9a";
        // $this->apiKeyId = "cl_apk_692432299fb86";
        // $this->tenantId = $this->getTenantId('');


        $this->apiUrls = self::BASE_URL;

        $this->client = new CaptainLearningClient(
            $this->apiKeyId,
            $this->apiKey,
            $this->apiUrls,
            $this->tenantId == '' ? null : $this->tenantId
        );
        $this->codeFormation = 'system_' . uniqid();
    }

    private function getTenantId(string $tenantId): string
    {
        return $tenantId;
    }

    public function testCreateFormationThrowsFormationBadRequestException(): void
    {
        $this->expectException(FormationBadRequestException::class);

        $formation = new FormationCreateRequest('test', '');

        $this->client->createFormation($formation);
    }
    public function testUpdateFormation(): void
    {
        //arrange
        $nameFormation = "Formation de test sans fichier";
        $code = $this->codeFormation;
        $formation = new FormationCreateRequest($nameFormation, $code);

        $this->client->createFormation($formation);

        $newNameFormation = "Formation de test update";
        $formationUpdate = new FormationUpdateRequest($newNameFormation, 'cl_formation_' . $code);

        //act
        $responseUpdateDTO = $this->client->updateFormation($formationUpdate);

        //assert
        $this->assertInstanceOf(FormationResponse::class, $responseUpdateDTO);
        $this->assertTrue($responseUpdateDTO->success);
        $this->assertTrue($responseUpdateDTO->data !== null);
    }
    public function testGetFormation(): void
    {
           //arrange
        $nameFormation = "Formation de test sans fichier";
        $code = $this->codeFormation;
        $formation = new FormationCreateRequest($nameFormation, $code);

        $this->client->createFormation($formation);
        //act
        $formationGetRequest = new FormationGetRequest('cl_formation_' . $code);
        $responseDTO = $this->client->getFormation($formationGetRequest);
        //assert
        $this->assertInstanceOf(FormationResponse::class, $responseDTO);
        $this->assertTrue($responseDTO->success);
    }
}
