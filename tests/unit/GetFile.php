<?php

namespace Tests\Unit;

use CaptainLearning\Domain\Shared\BaseDir;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use function Safe\file_get_contents;

class GetFile
{
    public const FILE_NAME = "exemple.pdf";
    private const RELATIVE_PATH = "/tests/Resources";

    public function buildUploadedFile(): UploadedFile
    {
        $file = new UploadedFile(
            $this->getFullPath(),
            self::FILE_NAME,
            'application/pdf',
            null,
            true
        );

        return $file;
    }

    public function getFullPath(): string
    {
        // return BaseDir::getFullPath(
        //     sprintf(
        //         '%s/%s',
        //         self::RELATIVE_PATH,
        //         self::FILE_NAME
        //     )
        // );
        return dirname(__DIR__, 2) . self::RELATIVE_PATH . '/' . self::FILE_NAME;
    }

    public function getContent(): string
    {
        return file_get_contents($this->getFullPath());
    }
}
