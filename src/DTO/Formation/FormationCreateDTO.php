<?php

namespace CaptainLearningPhp\DTO\Formation;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FormationCreateDTO
{
    public function __construct(
        public readonly string $intituleFormation,
        public readonly string $code,
        public readonly ?UploadedFile $file = null,
    ) {
    }
}
