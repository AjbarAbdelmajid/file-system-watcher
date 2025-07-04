<?php
namespace App\Infrastructure\Strategy;

use App\Core\Port\FileTypeStrategyInterface;
use App\Core\Port\EventType;
use App\Core\Port\HttpClientPort;

final class JsonStrategy implements FileTypeStrategyInterface
{
    public function __construct(private readonly HttpClientPort $http) {}

    public function supports(string $extension, EventType $type): bool
    {
        return $extension === 'json' && $type !== EventType::DELETE;
    }

    public function handle(string $fullPath): void
    {
        $data = json_decode(file_get_contents($fullPath), true);
        $this->http->postJson($fullPath, $data);
    }
}
