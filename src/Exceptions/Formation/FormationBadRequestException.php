<?php

namespace CaptainLearningPhp\Exceptions\Formation;

use Exception;

class FormationBadRequestException extends Exception
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
