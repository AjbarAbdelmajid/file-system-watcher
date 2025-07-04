<?php
namespace App\Core\Port;

use App\Core\Port\EventType;

interface FileEventProcessorInterface
{
    /**
     * Summary of process
     * @param string $path
     * @param EventType $eventType
     * @return void
     */
    public function process(string $path, EventType $eventType): void;
}