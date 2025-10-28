<?php

namespace CaptainLearningPhp\DTO\Formation;

class FormationCreateResponse
{
    public bool $success;
    /** @var array<string, mixed> | null */
    public ?array $data;
    public string $error;
    public string $error_message;

    /**
     * @param array<string, mixed> | null $data
     */
    public function __construct(
        bool $success,
        ?array $data,
        string $error,
        string $error_message
    ) {
        $this->success = $success;
        $this->data = $data;
        $this->error = $error;
        $this->error_message = $error_message;
    }
}
