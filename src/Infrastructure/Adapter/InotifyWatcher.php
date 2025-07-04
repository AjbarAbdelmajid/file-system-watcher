<?php
namespace App\Infrastructure\Adapter;

use App\Core\Application\Message\ProcessFileEventMessage;
use App\Core\Port\EventType;
use Symfony\Component\Messenger\MessageBusInterface;

final class InotifyWatcher
{
    public function __construct(
        private readonly MessageBusInterface $bus,
        private readonly string $watchedDir
    ) {}

    public function run(): void
    {
        if (!extension_loaded('inotify')) {
            throw new \RuntimeException('inotify extension is required');
        }

        $fd = inotify_init();
        stream_set_blocking($fd, true);

        // watch recursively
        $this->addWatches($fd, $this->watchedDir);

        while (true) {
            $events = inotify_read($fd);
            foreach ($events as $e) {
                $path = rtrim($this->watchedDir, '/')
                      . '/' 
                      . $e['name'];
                $type = $this->mapMaskToEventType($e['mask']);
                if ($type) {
                    $this->bus->dispatch(
                        new ProcessFileEventMessage($path, $type)
                    );
                }
                // handle new subdirectories
                if ($e['mask'] & IN_ISDIR && $e['mask'] & IN_CREATE) {
                    $this->addWatches($fd, $path);
                }
            }
        }
    }

    private function addWatches($fd, string $dir): void
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir)
        );
        foreach ($iterator as $file) {
            if ($file->isDir()) {
                inotify_add_watch(
                    $fd,
                    $file->getPathname(),
                    IN_CREATE | IN_MODIFY | IN_DELETE | IN_ISDIR
                );
            }
        }
    }

    private function mapMaskToEventType(int $mask): ?EventType
    {
        if ($mask & IN_CREATE) {
            return EventType::CREATE;
        }
        if ($mask & IN_MODIFY) {
            return EventType::MODIFY;
        }
        if ($mask & IN_DELETE) {
            return EventType::DELETE;
        }
        return null;
    }
}
