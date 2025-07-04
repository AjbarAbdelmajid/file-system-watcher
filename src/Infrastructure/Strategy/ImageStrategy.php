<?php
namespace App\Infrastructure\Strategy;

use App\Core\Port\EventType;
use App\Core\Port\ImageOptimizerPort;
use App\Core\Port\MovableFileTypeStrategyInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

#[AsTaggedItem('app.file_strategy')]
final class ImageStrategy implements MovableFileTypeStrategyInterface
{
    private const EXTENSIONS = ['jpg','jpeg','png','gif','webp'];

    public function __construct(
        private readonly ImageOptimizerPort $optimizer,
        private readonly LoggerInterface    $logger
    ) {}

    public function supports(string $extension, EventType $type): bool
    {
        return in_array($extension, self::EXTENSIONS, true)
            && $type !== EventType::DELETE;
    }

    public function handle(string $fullPath): void
    {
        $this->logger->info('Optimizing image', ['path' => $fullPath]);
        $this->optimizer->optimize($fullPath, $fullPath);
        $this->logger->info('Image optimization complete', ['path' => $fullPath]);
    }
}