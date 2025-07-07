<?php
namespace App\Infrastructure\Strategy;

use App\Core\Port\FileTypeStrategyInterface;
use App\Core\Port\EventType;
use App\Core\Port\TextAppenderPort;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

#[AsTaggedItem('app.file_strategy')]
final class TextStrategy implements FileTypeStrategyInterface
{
    public function __construct(
        private readonly TextAppenderPort $appender,
        private readonly LoggerInterface   $logger
    ) {}

    public function supports(string $extension, EventType $type): bool
    {
        return $extension === 'txt'
            && in_array($type, [EventType::CREATE, EventType::MODIFY], true);
    }

    public function handle(string $fullPath): void
    {
        $this->logger->info('Appending text to file', ['path' => $fullPath]);
        $this->appender->appendRandomText($fullPath);
        $this->logger->info('Text appended', ['path' => $fullPath]);
    }
}