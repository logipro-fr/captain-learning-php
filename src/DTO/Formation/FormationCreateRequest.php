<?php

namespace CaptainLearningPhp\DTO\Formation;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FormationCreateRequest
{
    public string $intituleFormation;
    public string $code;
    public ?UploadedFile $file;

    public function __construct(
        string $intituleFormation,
        string $code,
        ?UploadedFile $file = null
    ) {
        $this->intituleFormation = $intituleFormation;
        $this->code = $code;
        $this->file = $file;
    }
}
