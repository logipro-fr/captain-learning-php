<?php

namespace Tests\Unit;

use CaptainLearningPhp\CaptainLearningClient;
use CaptainLearningPhp\CaptainLearningClientFactory;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class CaptainLearningClientFactoryTest extends TestCase
{
    public function testCreateMockCaptainLearning(): void
    {
        $factory = new CaptainLearningClientFactory();
        $client = $factory->createMockCaptainLearning();
        $this->assertInstanceOf(CaptainLearningClient::class, $client);
    }
    public function testCreateMockCaptainLearningThrowsBadRequestException(): void
    {
        $factory = new CaptainLearningClientFactory();

        $reflection = new ReflectionClass($factory);
        $method = $reflection->getMethod('callableResponse');
        $method->setAccessible(true);

        $this->expectException(BadRequestException::class);

        $method->invoke($factory, 'POST', '/v1/badRoutes', []);
    }
    public function testCallableResponseThrowsOnNonPostMethod(): void
    {
        $factory = new CaptainLearningClientFactory();

        $reflection = new \ReflectionClass($factory);
        $method = $reflection->getMethod('callableResponse');
        $method->setAccessible(true);

        $this->expectException(BadRequestException::class);

        // Appel avec 'GET' au lieu de 'POST'
        $method->invoke($factory, 'GET', '/api/external/v1/formation', []);
    }
}
