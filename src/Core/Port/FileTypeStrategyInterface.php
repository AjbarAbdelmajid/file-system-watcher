<?php
namespace App\Core\Port;

use App\Core\Port\EventType;

interface FileTypeStrategyInterface
{
    /**
     * Return true if this handler wants to process a file with the given extension and event.
     *
     * @param string    $extension e.g. 'jpg', 'json', 'txt', 'zip'
     * @param EventType $type      create|modify|delete
     */
    public function supports(string $extension, EventType $type): bool;

    /**
     * Execute the handler’s logic for the given full file path.
     *
     * @param string $fullPath absolute path to the file or folder
     */
    public function handle(string $fullPath): void;
}
