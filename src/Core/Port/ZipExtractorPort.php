<?php
namespace App\Core\Port;

interface ZipExtractorPort
{
    /**
     * Extract a ZIP archive to a target directory.
     *
     * @param string $zipPath      Absolute path to the .zip file
     * @param string $destination  Absolute path to the folder where contents go
     */
    public function extract(string $zipPath, string $destination): void;
}