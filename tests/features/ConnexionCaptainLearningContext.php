<?php

namespace Tests\Features;

use Behat\Behat\Context\Context;
use CaptainLearningPhp\CaptainLearningClient;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Webmozart\Assert\Assert;

class ConnexionCaptainLearningContext extends TestCase implements Context
{
    private string $apiKey;
    private string $apiKeyId;
    private string $apiUrl;
    private string $tenantId;
    private string $token;


   /**
     * @Given je dispose d'une clé API :apikey
     */
    public function jeDisposeDuneCleApi(string $apikey): void
    {
        $this->apiKey = $apikey;
    }

    /**
     * @Given je dispose d'un apiKeyId :apiKeyId
     */
    public function jeDisposeDunApikeyid(string $apiKeyId): void
    {
        $this->apiKeyId = $apiKeyId;
    }

    /**
     * @Given l'URL de l'API est :apiUrl
     */
    public function lurlDeLapiEst(string $apiUrl): void
    {
        $this->apiUrl = $apiUrl;
    }

    /**
     * @Given le tenantId est :tenantId
     */
    public function leTenantidEst(string $tenantId): void
    {
        $this->tenantId = $tenantId;
    }

    /**
     * @When je me connecte à l'API Captain Learning
     */
    public function jeMeConnecteALapiCaptainLearning(): void
    {
        $httpClientMock = $this->getMockBuilder(HttpClientInterface::class)
        ->onlyMethods(['request','stream'])
        ->getMock();

        // Ajoute un mock de ResponseInterface
        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getContent')
        ->willReturn('{
                            "success": true,
                            "data": {
                                "token": "mock.token.value",
                                "apiKeyId": "cl_apk_123"
                            },
                            "error": "",
                            "error_message": ""
                        }');

        // Configure le mock pour retourner la réponse
        $httpClientMock->method('request')
        ->willReturn($responseMock);

        $client = new CaptainLearningClient(
            $this->apiKeyId,
            $this->apiKey,
            $this->apiUrl,
            $this->tenantId,
            $httpClientMock
        );
        // Utilise Reflection pour accéder à la propriété privée
        $reflection = new \ReflectionClass($client);
        $property = $reflection->getProperty('token');
        $property->setAccessible(true);

        /** @var string */
        $token = ($property->getValue($client));
        $this->token = $token;
    }



    /**
     * @Then je reçois un token de connexion valide
     */
    public function jeRecoisUnTokenDeConnexionValide(): void
    {

        if (empty($this->token)) {
            throw new \Exception('Le token de connexion est vide.');
        }
        $parts = explode('.', $this->token);
        Assert::count($parts, 3);
    }

    /**
     * @Then la connexion est établie avec succes
     */
    public function laConnexionEstEtablieAvecSucces(): void
    {
    }
}
