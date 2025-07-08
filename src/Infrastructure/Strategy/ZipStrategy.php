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
        private readonly LoggerInterface  $logger,
        private readonly string           $processedDir
    ) {}

    public function supports(string $extension, EventType $type): bool
    {
        return $extension === 'zip'
            && in_array($type, [EventType::CREATE, EventType::MODIFY], true);
    }

    public function handle(string $fullPath): void
    {
        $this->logger->info('Extracting ZIP archive', ['path' => $fullPath]);

        // Base name without extension
        $baseName = pathinfo($fullPath, PATHINFO_FILENAME);

        // Destination: processed/zip/<zip-name>/
        $destDir = "{$this->processedDir}/zip/{$baseName}";
        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }

        // Extract into that folder
        $this->extractor->extract($fullPath, $destDir);

        // Optionally move the zip itself into that same folder
        rename($fullPath, "{$destDir}/" . basename($fullPath));

        $this->logger->info('ZIP extraction complete', ['dest' => $destDir]);
    }
}