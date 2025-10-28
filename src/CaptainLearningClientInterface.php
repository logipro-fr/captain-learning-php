<?php

namespace CaptainLearningPhp;

use CaptainLearningPhp\DTO\Formation\FormationCreateRequest;
use CaptainLearningPhp\DTO\Formation\FormationCreateResponse;

interface CaptainLearningClientInterface
{
    public function createFormation(FormationCreateRequest $formation): FormationCreateResponse;
}
