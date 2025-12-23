<?php

namespace CaptainLearningPhp;

use CaptainLearningPhp\DTO\Formation\FormationCreateRequest;
use CaptainLearningPhp\DTO\Formation\FormationResponse;

interface CaptainLearningClientInterface
{
    public function createFormation(FormationCreateRequest $formation): FormationResponse;
}
