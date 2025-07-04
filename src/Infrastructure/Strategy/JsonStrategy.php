<?php
namespace App\Infrastructure\Strategy;

use App\Core\Port\FileTypeStrategyInterface;
use App\Core\Port\EventType;
use App\Core\Port\HttpClientPort;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

#[AsTaggedItem('app.file_strategy')]
final class JsonStrategy implements FileTypeStrategyInterface
{
    public function __construct(
        private readonly HttpClientPort $http,
        private readonly LoggerInterface $logger
    ) {}

    public function supports(string $extension, EventType $type): bool
    {
        return $extension === 'json' && $type !== EventType::DELETE;
    }

    public function handle(string $fullPath): void
    {
        $this->logger->info('Posting JSON file', ['path' => $fullPath]);
        $data = json_decode(file_get_contents($fullPath), true);
        $this->http->postJson($fullPath, $data);
        $this->logger->info('JSON POST complete', ['path' => $fullPath]);
    }
}
