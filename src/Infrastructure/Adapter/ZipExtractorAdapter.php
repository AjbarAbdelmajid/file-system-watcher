<?php

namespace App\Infrastructure\Adapter;

use App\Core\Port\ZipExtractorPort;

final class ZipExtractorAdapter implements ZipExtractorPort
{
    /**
     * Extract a ZIP archive into the given destination directory.
     *
     * @param string $zipPath      Absolute path to the .zip file
     * @param string $destination  Absolute path to extract contents into
     */
    public function extract(string $zipPath, string $destination): void
    {
        $zip = new \ZipArchive();

        if ($zip->open($zipPath) !== true) {
            throw new \RuntimeException("Failed to open ZIP archive: $zipPath");
        }

        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        $zip->extractTo($destination);
        $zip->close();
    }
}
