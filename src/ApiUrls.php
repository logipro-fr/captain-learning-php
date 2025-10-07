<?php

namespace CaptainLearningPhp;

class ApiUrls
{
    public const BASE_URL_PROD = 'https://app.captain-learning.com';

    public const CREATE_FORMATION = '/api/external/v1/formation';

    private string $baseUrl = self::BASE_URL_PROD;

    public function __construct(?string $baseUrl = null)
    {
        if ($baseUrl !== null) {
            $this->baseUrl = $baseUrl;
        }
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function createFormation(): string
    {
        return $this->getBaseUrl() . self::CREATE_FORMATION;
    }
}
