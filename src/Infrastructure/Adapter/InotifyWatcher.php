<?php
// src/Infrastructure/Adapter/InotifyWatcher.php
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
        // Tell the user we’re monitoring
        // Note: your WatchCommand already prints this, so you can omit buffering here.

        // Build the inotifywait command:
        $cmd = [
            'inotifywait',
            '-m',                // monitor continuously
            '-r',                // recursive
            '-e', 'create',
            '-e', 'modify',
            '-e', 'delete',
            '--format', '%e|%w%f',
            $this->watchedDir,
        ];

        $process = proc_open($cmd, [
            1 => ['pipe', 'w'],  // stdout
            2 => ['pipe', 'w'],  // stderr
        ], $pipes);

        if (!is_resource($process)) {
            throw new \RuntimeException('Failed to launch inotifywait');
        }

        $stdout = $pipes[1];
        while (($line = fgets($stdout)) !== false) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            // inotifywait outputs e.g. "CREATE|/watched/incoming/imgs/foo.jpg"
            [$events, $path] = explode('|', $line, 2);
            $firstEvent = explode(',', $events)[0];

            $type = match ($firstEvent) {
                'CREATE' => EventType::CREATE,
                'MODIFY' => EventType::MODIFY,
                'DELETE' => EventType::DELETE,
                default   => null,
            };

            if ($type === null) {
                continue;
            }

            $this->bus->dispatch(new ProcessFileEventMessage($path, $type));
        }

        proc_close($process);
    }
}
