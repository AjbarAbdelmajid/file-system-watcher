<?php
namespace App\Infrastructure\Strategy;

use App\Core\Port\FileTypeStrategyInterface;
use App\Core\Port\EventType;
use App\Core\Port\ZipExtractorPort;

final class ZipStrategy implements FileTypeStrategyInterface
{
    public function __construct(private readonly ZipExtractorPort $extractor) {}

    public function supports(string $extension, EventType $type): bool
    {
        return $extension === 'zip' && $type !== EventType::DELETE;
    }

    public function handle(string $fullPath): void
    {
        $destination = dirname($fullPath);
        $this->extractor->extract($fullPath, $destination);
    }
}