<?php
namespace App\Infrastructure\Strategy;

use App\Core\Port\FileTypeStrategyInterface;
use App\Core\Port\EventType;
use App\Core\Port\MemeFetcherPort;

final class DeleteStrategy implements FileTypeStrategyInterface
{
    public function __construct(private readonly MemeFetcherPort $fetcher) {}

    public function supports(string $extension, EventType $type): bool
    {
        return $type === EventType::DELETE;
    }

    public function handle(string $fullPath): void
    {
        $this->fetcher->fetchAndSave($fullPath);
    }
}