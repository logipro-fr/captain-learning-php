<?php

namespace CaptainLearningPhp\Domain;

use function Safe\realpath;

class BaseDir
{
    private const DIRECTORY_PATH = __DIR__ . '/../..';

    // private const CONTAINER_DATA_PATH = "/var/captain-learning-webapp/data";
    // public const KEYSS_JWT_PATH = "/config/jwt";

    public static function getFullPath(string $relativePath): string
    {
        $basePath = sprintf("%s/", realpath(self::DIRECTORY_PATH));
        return $basePath . $relativePath;
    }

    // public static function getDataFullPath(): string
    // {
    //     return self::CONTAINER_DATA_PATH;
    // }

    // public static function getKeysJWTFullPath(): string
    // {
    //     $fullPath = self::getFullPath(self::KEYSS_JWT_PATH);
    //     return realpath($fullPath);
    // }
}
