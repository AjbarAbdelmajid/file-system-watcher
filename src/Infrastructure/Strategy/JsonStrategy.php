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
        private readonly HttpClientPort  $client,
        private readonly string          $processedDir,
        private readonly LoggerInterface $logger
    ) {}

    public function supports(string $extension, EventType $type): bool
    {
        return $extension === 'json'
            && in_array($type, [EventType::CREATE, EventType::MODIFY], true);
    }

    public function handle(string $fullPath): void
    {
        $this->logger->info('Posting JSON', ['path'=>$fullPath]);
        $data = json_decode(file_get_contents($fullPath), true);
        $this->client->postJson($fullPath, $data);

        // now move it into processed/json/
        $destDir = $this->processedDir . '/json';
        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }
        $dest = $destDir . '/' . basename($fullPath);
        rename($fullPath, $dest);

        $this->logger->info('Moved JSON to processed', ['dest'=>$dest]);
    }
}
