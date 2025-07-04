<?php
namespace App\Core\Application\UseCase;

use App\Core\Port\EventType;
use App\Core\Port\FileTypeStrategyInterface;
use App\Core\Port\MovableFileTypeStrategyInterface;
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
        $this->logger->info('Received filesystem event', [
            'path' => $fullPath,
            'type' => $type->value,
        ]);

        $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

        foreach ($this->strategies as $strategy) {
            if (!$strategy->supports($extension, $type)) {
                continue;
            }

            $this->logger->info('Dispatching to strategy', [
                'strategy'  => get_class($strategy),
                'extension' => $extension,
                'type'      => $type->value,
            ]);

            try {
                // execute the core handling
                $strategy->handle($fullPath);
                $this->logger->info('Strategy completed', [
                    'strategy' => get_class($strategy),
                    'path'     => $fullPath,
                ]);

                // if this strategy is “movable”, relocate the file
                if ($strategy instanceof MovableFileTypeStrategyInterface) {
                    $this->moveOnSuccess($fullPath, $extension);
                }

            } catch (\Throwable $e) {
                $this->logger->error('Error in strategy', [
                    'strategy'  => get_class($strategy),
                    'path'      => $fullPath,
                    'exception' => $e,
                ]);
                $this->moveOnError($fullPath);
            }

            // once one strategy handled it, we’re done
            return;
        }

        $this->logger->warning('No strategy found for file', [
            'path'      => $fullPath,
            'extension' => $extension,
            'type'      => $type->value,
        ]);
    }

    private function moveOnSuccess(string $fullPath, string $ext): void
    {
        $rel = ltrim(str_replace($this->watchedDir . '/', '', $fullPath), '/');
        $target = "{$this->processedDir}/{$ext}/" . basename($rel);

        if (!is_dir(dirname($target))) {
            mkdir(dirname($target), 0755, true);
        }
        rename($fullPath, $target);
        $this->logger->info('Moved file to processed', ['destination' => $target]);
    }

    private function moveOnError(string $fullPath): void
    {
        $target = $this->errorDir . '/' . basename($fullPath);
        if (!is_dir(dirname($target))) {
            mkdir(dirname($target), 0755, true);
        }
        if (file_exists($fullPath)) {
            rename($fullPath, $target);
        }
        $this->logger->info('Moved file to error', ['destination' => $target]);
    }

}
