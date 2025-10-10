<?php

namespace CaptainLearningPhp\Services;

use CaptainLearningPhp\ApiUrls;
use CaptainLearningPhp\DTO\Formation\FormationCreateDTO;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FormationService
{
    private HttpClientInterface $httpClient;
    private ApiUrls $apiUrls;

    public function __construct(
        HttpClientInterface $httpClient,
        ApiUrls $apiUrls
    ) {
        $this->httpClient = $httpClient;
        $this->apiUrls = $apiUrls;
    }

    public function create(FormationCreateDTO $formation): string
    {
        $body = [
        'intituleFormation' => $formation->intituleFormation,
        'code' => $formation->code,
        ];

        if ($formation->file !== null) {
            $body['document'] = DataPart::fromPath(
                $formation->file->getPathname(),
                $formation->file->getClientOriginalName(),
                $formation->file->getMimeType()
            );
        }

        $formData = new FormDataPart($body);


        $response = $this->httpClient->request('POST', $this->apiUrls->createFormation(), [
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => 'CaptainLearningClient/1.0'
            ] + $formData->getPreparedHeaders()->toArray(),
            'body' => $formData->bodyToIterable()
        ]);

        return $response->getContent(false);
    }
}
