<?php

namespace Tests\Unit;

use CaptainLearningPhp\ApiUrls;
use PHPUnit\Framework\TestCase;

class ApiUrlsTest extends TestCase
{
    public function testGetBaseUrl(): void
    {
        $apiUrls = new ApiUrls();
        $baseUrl = $apiUrls->getBaseUrl();
        $this->assertSame(ApiUrls::BASE_URL_PROD, $baseUrl);
    }

    public function testNewBaseUrl(): void
    {
        $newBaseUrl = "http://nginx";
        $apiUrls = new ApiUrls($newBaseUrl);
        $baseUrl = $apiUrls->getBaseUrl();
        $this->assertSame($newBaseUrl, $baseUrl);
    }
    public function testCreateUriFormation(): void
    {
        $this->assertSame(
            ApiUrls::BASE_URL_PROD . ApiUrls::CREATE_FORMATION,
            (new ApiUrls())->createFormation()
        );
    }
}
