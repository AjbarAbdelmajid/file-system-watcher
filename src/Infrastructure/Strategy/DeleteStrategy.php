<?php
namespace App\Infrastructure\Strategy;

use App\Core\Port\FileTypeStrategyInterface;
use App\Core\Port\EventType;
use App\Core\Port\MemeFetcherPort;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

#[AsTaggedItem('app.file_strategy')]
final class DeleteStrategy implements FileTypeStrategyInterface
{
    public function __construct(
        private readonly MemeFetcherPort $fetcher,
        private readonly LoggerInterface $logger
    ) {}

    public function supports(string $extension, EventType $type): bool
    {
        return $type === EventType::DELETE;
    }

    public function handle(string $fullPath): void
    {
        $this->logger->info('Delete detected; fetching meme', ['path' => $fullPath]);
        $this->fetcher->fetchAndSave($fullPath);
        $this->logger->info('Meme replacement saved', ['path' => $fullPath]);
    }
}