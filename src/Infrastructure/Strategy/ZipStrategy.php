<?php
namespace App\Infrastructure\Strategy;

use App\Core\Port\FileTypeStrategyInterface;
use App\Core\Port\EventType;
use App\Core\Port\ZipExtractorPort;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

#[AsTaggedItem('app.file_strategy')]
final class ZipStrategy implements FileTypeStrategyInterface
{
    public function __construct(
        private readonly ZipExtractorPort $extractor,
        private readonly LoggerInterface  $logger
    ) {}

    public function supports(string $extension, EventType $type): bool
    {
        return $extension === 'zip'
            && in_array($type, [EventType::CREATE, EventType::MODIFY], true);
    }

    public function handle(string $fullPath): void
    {
        $this->logger->info('Extracting ZIP archive', ['path' => $fullPath]);
        $this->extractor->extract($fullPath, dirname($fullPath));
        $this->logger->info('ZIP extraction complete', ['path' => dirname($fullPath)]);
    }
}