<?php

namespace Tests\Integration;

use CaptainLearningPhp\ApiUrls;
use CaptainLearningPhp\CaptainLearningClient;
use CaptainLearningPhp\DTO\Formation\FormationCreateRequest;
use CaptainLearningPhp\Exceptions\FormationBadRequestException;
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
        $this->client = new CaptainLearningClient(
            new CurlHttpClient(),
            new ApiUrls(self::BASE_URL)
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
