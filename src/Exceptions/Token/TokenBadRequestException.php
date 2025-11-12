<?php

namespace CaptainLearningPhp\Exceptions\Token;

use Exception;

class TokenBadRequestException extends Exception
{
    public function __construct(string $message, int $code = 400)
    {
        parent::__construct(
            sprintf(
                "Bad request exception with content '%s'",
                $message,
            ),
            $code
        );
    }
}
