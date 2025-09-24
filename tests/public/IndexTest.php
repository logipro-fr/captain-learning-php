<?php

namespace Tests\CaptainLearningPhp;

use PHPUnit\Framework\TestCase;

class IndexTest extends TestCase
{
    public function testEchoWelcome(): void
    {
        $this->expectOutputString('Welcome to captain-learning-php!');
        require getcwd() . '/src/public/index.php';
    }
}
