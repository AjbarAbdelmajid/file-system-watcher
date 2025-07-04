<?php
namespace App\Core\Port;

interface MemeFetcherPort
{
    /**
     * Fetch a random meme image from the Meme API and save it to the given path.
     *
     * @param string $targetPath Absolute path where the meme image should be saved
     */
    public function fetchAndSave(string $targetPath): void;
}