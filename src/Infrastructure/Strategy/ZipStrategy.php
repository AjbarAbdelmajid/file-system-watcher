<?php
namespace App\Infrastructure\Strategy;

use App\Core\Port\FileTypeStrategyInterface;
use App\Core\Port\EventType;
use App\Core\Port\ZipExtractorPort;
use Psr\Log\LoggerInterface;

final class ZipStrategy implements FileTypeStrategyInterface
{
    public function __construct(
        private readonly ZipExtractorPort $extractor,
        private readonly LoggerInterface  $logger
    ) {}

    public function supports(string $extension, EventType $type): bool
    {
        return $extension === 'zip' && $type !== EventType::DELETE;
    }

    public function handle(string $fullPath): void
    {
        $this->logger->info('Extracting ZIP archive', ['path' => $fullPath]);
        $destination = dirname($fullPath);
        $this->extractor->extract($fullPath, $destination);
        $this->logger->info('ZIP extraction complete', ['destination' => $destination]);
    }
}