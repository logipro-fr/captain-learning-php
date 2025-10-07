<?php

namespace Tests\Unit;

use CaptainLearningPhp\ApiUrls;
use PHPUnit\Framework\TestCase;

class ApiUrlsTest extends TestCase
{
    // private string $urlApi;

    // public function setUp(): void
    // {
    //    /** @var array<string, string> $_ENV */
    //     $this->urlApi = $_ENV['URL_API'];
    //     echo "URL_API avant test : " . $this->urlApi . "\n";
    // }
    // public function tearDown(): void
    // {
    //     if (!isset($_ENV['URL_API'])) {
    //         $_ENV['URL_API'] = $this->urlApi;
    //     }
    // }

    // public function testCreateUriWhithoutEnvExist(): void
    // {
    //     // unset($_ENV['URL_API']);
    //     $this->assertEquals(
    //         "http://172.17.0.1:11780/api/external/v1/formation",
    //         (new ApiUrls())->createFormation()
    //     );
    // }
    public function testGetBaseUrl(): void
    {
        $apiUrls = new ApiUrls();
        $baseUrl = $apiUrls->getBaseUrl();
        $this->assertEquals("https://app.captain-learning.com", $baseUrl);
    }

    public function testNewBaseUrl(): void
    {
        $newBaseUrl = "http://nginx";
        $apiUrls = new ApiUrls($newBaseUrl);
        $baseUrl = $apiUrls->getBaseUrl();
        $this->assertEquals($newBaseUrl, $baseUrl);
    }
    public function testCreateUriFormation(): void
    {
        $this->assertEquals(
            "https://app.captain-learning.com/api/external/v1/formation",
            (new ApiUrls())->createFormation()
        );
    }
}
