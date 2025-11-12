<?php

namespace Tests\Unit\Exceptions\Token;

use CaptainLearningPhp\Exceptions\Token\TokenBadRequestException;
use PHPUnit\Framework\TestCase;

class TokenBadRequestExceptionTest extends TestCase
{
    public function testBadRequestException(): void
    {
        $sut = new TokenBadRequestException('test message');

        $this->assertInstanceOf(TokenBadRequestException::class, $sut);
        $this->assertEquals("Bad request exception with content 'test message'", $sut->getMessage());
    }
}
