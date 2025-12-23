<?php

namespace CaptainLearningPhp\DTO\Formation;

class FormationGetRequest
{
    public string $codeFormation;
    public function __construct(
        string $codeFormation
    ) {
        $this->codeFormation = $codeFormation;
    }
}
