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
        private readonly string           $processedDir,
        private readonly LoggerInterface   $logger
    ) {}

    public function supports(string $extension, EventType $type): bool
    {
        return $extension === 'txt'
            && in_array($type, [EventType::CREATE, EventType::MODIFY], true);
    }

    public function handle(string $fullPath): void
    {
        $this->logger->info('Appending text', ['path'=>$fullPath]);

        // then move into processed/txt/
        $destDir = $this->processedDir . '/txt';
        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }
        $dest = $destDir . '/' . basename($fullPath);
        rename($fullPath, $dest);
        $this->appender->appendRandomText($dest);

        $this->logger->info('Moved TXT to processed', ['dest'=>$dest]);
    }
}