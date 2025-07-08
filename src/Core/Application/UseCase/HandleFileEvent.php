<?php
namespace App\Core\Application\UseCase;

use App\Core\Port\EventType;
use App\Core\Port\FileTypeStrategyInterface;
use Psr\Log\LoggerInterface;

final class HandleFileEvent
{
    /** @param FileTypeStrategyInterface[] $strategies */
    public function __construct(
        private iterable $strategies,
        private readonly string $watchedDir,
        private readonly string $processedDir,
        private readonly string $errorDir,
        private readonly LoggerInterface $logger
    ) {}
    
    public function process(string $fullPath, EventType $type): void
    {
        $this->logger->info('Event', ['path'=>$fullPath,'type'=>$type->value]);
        $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

        foreach ($this->strategies as $strategy) {
            if (!$strategy->supports($ext, $type)) {
                continue;
            }

            $this->logger->info('Using', ['strategy'=> get_class($strategy)]);
            try {
                $strategy->handle($fullPath);
                $this->logger->info('Done', ['strategy'=> get_class($strategy)]);
            } catch (\Throwable $e) {
                $this->logger->error('Error', ['strategy'=> get_class($strategy), 'err'=>$e->getMessage()]);
            }

            return;
        }

        $this->logger->warning('No strategy for', ['ext'=>$ext,'type'=>$type->value]);
    }
}
