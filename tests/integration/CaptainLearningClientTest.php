<?php

namespace Tests\Integration;

use CaptainLearningPhp\CaptainLearningClient;
use CaptainLearningPhp\DTO\Formation\FormationCreateRequest;
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
        $this->apiKey = "sk_24b3749d68a3c63e5106807c573ee904bee93ae4355ddd016fb0485676652666";
        $this->apiKeyId = "cl_apk_6924259755514";
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
        $this->codeFormation = uniqid();
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
}
