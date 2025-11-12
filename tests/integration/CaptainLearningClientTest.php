<?php

namespace Tests\Integration;

use CaptainLearningPhp\ApiUrls;
use CaptainLearningPhp\CaptainLearningClient;
use CaptainLearningPhp\DTO\Formation\FormationCreateRequest;
use CaptainLearningPhp\Exceptions\Formation\FormationBadRequestException;
use Symfony\Component\HttpClient\CurlHttpClient;
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
        $this->apiKey = "sk_7a5f1b27ad33f4b6455ce8f96ca90bf20c5bde61f6c3462918ae9bfb4c768c77";
        $this->apiKeyId = "cl_apk_690b25d814d2b";
        $this->client = new CaptainLearningClient(
            $this->apiKeyId,
            $this->apiKey,
            self::BASE_URL
        );
        $this->codeFormation = uniqid();
    }

    public function testCreateFormationThrowsFormationBadRequestException(): void
    {
        $this->expectException(FormationBadRequestException::class);

        $formation = new FormationCreateRequest('test', '');

        $this->client->createFormation($formation);
    }
}
