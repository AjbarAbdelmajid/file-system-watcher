<?php
namespace App\Infrastructure\Strategy;

use App\Core\Port\EventType;
use App\Core\Port\FileTypeStrategyInterface;
use App\Core\Port\ImageOptimizerPort;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

#[AsTaggedItem('app.file_strategy')]
final class ImageStrategy implements FileTypeStrategyInterface
{
    private const EXTENSIONS = ['jpg','jpeg','png','gif','webp'];

    public function __construct(
        private readonly ImageOptimizerPort $optimizer,
        private readonly string             $processedDir,
        private readonly LoggerInterface    $logger
    ) {}

    public function supports(string $extension, EventType $type): bool
    {
        return in_array($extension, self::EXTENSIONS, true)
            && in_array($type, [EventType::CREATE, EventType::MODIFY], true);
    }

    public function handle(string $fullPath): void
    {
        $this->logger->info('Optimizing image', ['path' => $fullPath]);

        // Prepare destination folder: processed/imgs
        $destDir = $this->processedDir . '/imgs';
        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }

        $dest = $destDir . '/' . basename($fullPath);

        // Run the optimizer (source → target)
        $this->optimizer->optimize($fullPath, $dest);

        // Overwrite/move original
        rename($fullPath, $dest);

        $this->logger->info('Image moved to processed', ['dest' => $dest]);
    }
}