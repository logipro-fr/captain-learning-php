<?php

namespace Tests\Unit\Services;

use CaptainLearningPhp\ApiUrls;
use CaptainLearningPhp\DTO\Formation\FormationCreateDTO;
use CaptainLearningPhp\Services\FormationService;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Tests\Unit\GetFile;

class FormationServiceTest extends TestCase
{
    public function testCreateFormationRequiresIntituleAndCode(): void
    {
        /** @var HttpClientInterface&\PHPUnit\Framework\MockObject\MockObject $httpClientMock */
        $httpClientMock = $this->getMockBuilder(HttpClientInterface::class)
            ->onlyMethods(['request', 'stream'])
            ->getMock();

        /** @var ApiUrls&\PHPUnit\Framework\MockObject\MockObject $apiUrlsMock */
        $apiUrlsMock = $this->getMockBuilder(ApiUrls::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var ResponseInterface&\PHPUnit\Framework\MockObject\MockObject $responseMock */
        $responseMock = $this->getMockBuilder(ResponseInterface::class)
            ->onlyMethods([
                'getContent',
                'getStatusCode',
                'getHeaders',
                'toArray',
                'cancel',
                'getInfo'
            ])
            ->getMock();

        $responseMock->method('getContent')->willReturn('{"id": 1}');

        $httpClientMock->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo('POST'),
                $this->anything(),
                $this->callback(function ($options) {
                    if (!is_array($options)) {
                        return false;
                    }

                    // Vérifie la présence des headers
                    $headers = $options['headers'] ?? [];
                    if (!is_array($headers)) {
                        return false;
                    }

                    // Vérifie Accept et User-Agent
                    if (!isset($headers['Accept']) || $headers['Accept'] !== 'application/json') {
                        return false;
                    }

                    if (!isset($headers['User-Agent']) || $headers['User-Agent'] !== 'CaptainLearningClient/1.0') {
                        return false;
                    }

                    // Vérifie que le Content-Type multipart est présent
                    $hasMultipartHeader = false;
                    foreach ($headers as $key => $value) {
                        if (
                            $key === 'Content-Type'
                            && is_string($value)
                            && strpos($value, 'multipart/form-data') !== false
                        ) {
                            $hasMultipartHeader = true;
                            break;
                        }
                        if (is_string($value) && strpos($value, 'Content-Type: multipart/form-data') === 0) {
                            $hasMultipartHeader = true;
                            break;
                        }
                    }

                    if (!$hasMultipartHeader) {
                        return false;
                    }

                    // Vérifie que le body est bien un itérable
                    if (!isset($options['body']) || !is_iterable($options['body'])) {
                        return false;
                    }

                    // Convertit le Generator en string pour vérifier le contenu
                    $bodyContent = '';
                    foreach ($options['body'] as $chunk) {
                        if (is_string($chunk)) {
                            $bodyContent .= $chunk;
                        }
                    }

                    // Vérifie que intituleFormation et code sont présents dans le body multipart
                    if (strpos($bodyContent, 'name="intituleFormation"') === false) {
                        return false;
                    }

                    if (strpos($bodyContent, 'name="code"') === false) {
                        return false;
                    }

                    return true;
                })
            )
            ->willReturn($responseMock);

        $service = new FormationService($httpClientMock, $apiUrlsMock);

        $dto = new FormationCreateDTO('Titre formation', 'CODE123');
        $service->create($dto);
    }

    public function testCreateFormationReturnsRawContentEvenOnError(): void
    {
        $httpClientMock = $this->getMockBuilder(HttpClientInterface::class)
            ->onlyMethods(['request','stream'])
            ->getMock();

        $apiUrlsMock = $this->getMockBuilder(ApiUrls::class)
            ->disableOriginalConstructor()
            ->getMock();

        $responseMock = $this->getMockBuilder(ResponseInterface::class)
            ->onlyMethods([
                'getContent',
                'getStatusCode',
                'getHeaders',
                'toArray',
                'cancel',
                'getInfo'
            ])
            ->getMock();
        // Simule une réponse avec un code d'erreur
        $responseMock->expects($this->once())
            ->method('getContent')
            ->with(false)
            ->willReturn('error content');

        $httpClientMock->expects($this->once())
            ->method('request')
            ->willReturn($responseMock);

        $service = new FormationService($httpClientMock, $apiUrlsMock);

        $dto = new FormationCreateDTO('Titre', 'CODE123');
        $result = $service->create($dto);

        $this->assertSame('error content', $result);
    }

    public function testCreateFormationSendsAcceptHeader(): void
    {
        $httpClientMock = $this->getMockBuilder(HttpClientInterface::class)
        ->onlyMethods(['request', 'stream'])
        ->getMock();

        $apiUrlsMock = $this->getMockBuilder(ApiUrls::class)
        ->disableOriginalConstructor()
        ->getMock();

        $httpClientMock->expects($this->once())
        ->method('request')
        ->with(
            $this->equalTo('POST'),
            $this->anything(),
            $this->callback(function ($options) {
                if (!is_array($options)) {
                    return false;
                }
                $headers = $options['headers'] ?? [];
                if (!is_array($headers)) {
                    return false;
                }
                return isset($headers['Accept'])
                && $headers['Accept'] === 'application/json';
            })
        )
        ->willReturn($this->createMock(ResponseInterface::class));

        $service = new FormationService($httpClientMock, $apiUrlsMock);
        $dto = new FormationCreateDTO('Titre', 'CODE123');
        $service->create($dto);
    }
    public function testCreateWithfile(): void
    {
        $httpClientMock = $this->getMockBuilder(HttpClientInterface::class)
            ->onlyMethods(['request', 'stream'])
            ->getMock();

        $apiUrlsMock = $this->getMockBuilder(ApiUrls::class)
            ->disableOriginalConstructor()
            ->getMock();

        $responseMock = $this->getMockBuilder(ResponseInterface::class)
            ->onlyMethods([
                'getContent',
                'getStatusCode',
                'getHeaders',
                'toArray',
                'cancel',
                'getInfo'
            ])
            ->getMock();

        $responseMock->method('getContent')->willReturn('{"id": 1}');

        $httpClientMock->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo('POST'),
                $this->anything(),
                $this->callback(function ($options) {
                    if (!is_array($options)) {
                        return false;
                    }

                    // Vérifie la présence des headers
                    $headers = $options['headers'] ?? [];
                    if (!is_array($headers)) {
                        return false;
                    }

                    // Vérifie que le Content-Type multipart est présent
                    // (peut être dans une clé numérique ou associative)
                    $hasMultipartHeader = false;
                    foreach ($headers as $key => $value) {
                        // Cas 1: header avec clé associative 'Content-Type'
                        if (
                            $key === 'Content-Type'
                            && is_string($value)
                            && strpos($value, 'multipart/form-data') !== false
                        ) {
                            $hasMultipartHeader = true;
                            break;
                        }
                        // Cas 2: header sous forme de chaîne dans un index numérique
                        if (is_string($value) && strpos($value, 'Content-Type: multipart/form-data') === 0) {
                            $hasMultipartHeader = true;
                            break;
                        }
                    }

                    if (!$hasMultipartHeader) {
                        return false;
                    }

                    // Vérifie que le body est bien un itérable (Generator) quand un fichier est présent
                    if (!isset($options['body']) || !is_iterable($options['body'])) {
                        return false;
                    }

                    return true;
                })
            )
            ->willReturn($responseMock);

        $service = new FormationService($httpClientMock, $apiUrlsMock);
        $getFile = new GetFile();
        $file = $getFile->buildUploadedFile();

        $dto = new FormationCreateDTO('Titre', 'CODE123', $file);
        $service->create($dto);
    }
}
