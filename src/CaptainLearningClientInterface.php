<?php

namespace CaptainLearningPhp;

use CaptainLearningPhp\DTO\Formation\FormationCreateDTO;

interface CaptainLearningClientInterface
{
    public function createFormation(FormationCreateDTO $formation): string;
}
