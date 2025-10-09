<?php

require dirname(__DIR__, 2) . '/vendor/autoload.php';

use CaptainLearningPhp\ApiUrls;
use CaptainLearningPhp\CaptainLearningClient;
use CaptainLearningPhp\DTO\Formation\FormationCreateDTO;
use Symfony\Component\HttpClient\CurlHttpClient;
use Tests\Unit\GetFile;

$getFile = new GetFile();
$file = $getFile->buildUploadedFile();

$nameFormation = "Formation de test depuis index.php";
$codeFormation = "F" . uniqid();
$formation = new FormationCreateDTO(
    $nameFormation,
    $codeFormation,
    $file
);

$apiUrl = new ApiUrls('http://172.17.0.1:11780');

$client = new CaptainLearningClient(new CurlHttpClient(), $apiUrl);
$response = $client->createFormation($formation);

echo $response;
