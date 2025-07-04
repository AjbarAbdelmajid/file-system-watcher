<?php
namespace App\Infrastructure\Strategy;

use App\Core\Port\FileTypeStrategyInterface;
use App\Core\Port\EventType;
use App\Core\Port\TextAppenderPort;

final class TextStrategy implements FileTypeStrategyInterface
{
    public function __construct(private readonly TextAppenderPort $appender) {}

    public function supports(string $extension, EventType $type): bool
    {
        return $extension === 'txt' && $type !== EventType::DELETE;
    }

    public function handle(string $fullPath): void
    {
        $this->appender->appendRandomText($fullPath);
    }
}