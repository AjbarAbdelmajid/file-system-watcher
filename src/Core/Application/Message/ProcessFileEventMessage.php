<?php
namespace App\Core\Application\Message;

use App\Core\Port\EventType;

final class ProcessFileEventMessage
{
    public function __construct(
        public readonly string $path,
        public readonly EventType $eventType,
    ) {}
}