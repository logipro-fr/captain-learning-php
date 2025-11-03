<?php

namespace Tests\Unit;

use Symfony\Component\HttpFoundation\File\UploadedFile;

use function Safe\file_get_contents;

/**
 * Classe utilitaire pour fournir un fichier UploadedFile
 * destiné aux tests unitaires.
 *
 * Les méthodes permettent de récupérer un objet UploadedFile
 * ou le contenu brut du fichier exemple.pdf situé dans tests/Resources.
 */
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
        return dirname(__DIR__, 2) . self::RELATIVE_PATH . '/' . self::FILE_NAME;
    }

    public function getContent(): string
    {
        return file_get_contents($this->getFullPath());
    }
}
