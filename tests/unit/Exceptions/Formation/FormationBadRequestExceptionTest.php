<?php

namespace Tests\Unit\Exceptions;

use CaptainLearningPhp\Exceptions\Formation\FormationBadRequestException;
use PHPUnit\Framework\TestCase;

class FormationBadRequestExceptionTest extends TestCase
{
    public function testBadRequestException(): void
    {
        $message = 'test message';
        $sut = new FormationBadRequestException($message);

        $this->assertInstanceOf(FormationBadRequestException::class, $sut);
        $this->assertStringStartsWith(sprintf("Bad request exception with content '%s'", $message), $sut->getMessage());
    }
}
