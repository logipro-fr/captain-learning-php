<?php

namespace CaptainLearningPhp\Services\Token;

use CaptainLearningPhp\ApiUrls;
use CaptainLearningPhp\Exceptions\Token\TokenBadRequestException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function Safe\json_decode;

class CreateTokenService
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

    public function execute(string $apiKeyId, string $apiKey): string
    {
        try {
            $response = $this->httpClient->request(
                "POST",
                $this->apiUrls->createToken(),
                [
                    "body" => json_encode([
                        "apiKeyId" => $apiKeyId,
                        "apiKey" => $apiKey
                    ]),
                ]
            );
        } catch (\Throwable $th) {
            throw new TokenBadRequestException($th->getMessage());
        }

        /**
         * @var object{
         *     success: bool,
         *     data: object{token: string, apiKeyId: string},
         *     error: string,
         *     error_message: string
         * } $data
         */
        $data = json_decode($response->getContent());

        if (!$data->success || $data->data == null) {
            throw new TokenBadRequestException($data->error_message);
        }
        return $data->data->token;
    }
}
