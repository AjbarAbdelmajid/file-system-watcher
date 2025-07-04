<?php
namespace App\Core\Port;

interface TextAppenderPort
{
    /**
     * Append random text (e.g. from Bacon Ipsum) to the end of a text file.
     *
     * @param string $filePath Absolute path to the .txt file
     */
    public function appendRandomText(string $filePath): void;
}