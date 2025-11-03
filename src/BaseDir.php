<?php

namespace CaptainLearningPhp;

use function Safe\realpath;

class BaseDir
{
    private const DIRECTORY_PATH = __DIR__ . '/..';

    public static function getFullPath(string $relativePath): string
    {
        $basePath = sprintf("%s/", realpath(self::DIRECTORY_PATH));
        return $basePath . $relativePath;
    }
}
