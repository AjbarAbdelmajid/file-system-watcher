<?php
namespace App\Core\Application\UseCase;

use App\Core\Port\EventType;
use App\Core\Port\FileTypeStrategyInterface;
use App\Core\Port\MovableFileTypeStrategyInterface;

final class HandleFileEvent
{
    /** @param FileTypeStrategyInterface[] $strategies */
    public function __construct(
        private iterable $strategies,
        private readonly string $watchedDir,
        private readonly string $processedDir,
        private readonly string $errorDir,
    ) {}
    
    public function process(string $fullPath, EventType $type): void
    {
        $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

        foreach ($this->strategies as $strategy) {
            if (!$strategy->supports($extension, $type)) {
                continue;
            }

            try {
                // execute the core handling
                $strategy->handle($fullPath);

                // if this strategy is “movable”, relocate the file
                if ($strategy instanceof MovableFileTypeStrategyInterface) {
                    $this->moveOnSuccess($fullPath, $extension);
                }

            } catch (\Throwable $e) {
                $this->moveOnError($fullPath);
            }

            // once one strategy handled it, we’re done
            return;
        }
    }

    private function moveOnSuccess(string $fullPath, string $ext): void
    {
        $rel = ltrim(str_replace($this->watchedDir . '/', '', $fullPath), '/');
        $target = "{$this->processedDir}/{$ext}/" . basename($rel);

        if (!is_dir(dirname($target))) {
            mkdir(dirname($target), 0755, true);
        }
        rename($fullPath, $target);
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
    }

}
