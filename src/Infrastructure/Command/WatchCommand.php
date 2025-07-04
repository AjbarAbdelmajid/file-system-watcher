<?php
// src/Infrastructure/Command/WatchCommand.php
namespace App\Infrastructure\Command;

use App\Infrastructure\Adapter\InotifyWatcher;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WatchCommand extends Command
{
    protected static $defaultName = 'file:watch';
    protected static $defaultDescription = 'Start the real-time file system watcher.';

    public function __construct(
        private readonly InotifyWatcher    $watcher,
        private readonly string            $watchedDir,
        private readonly LoggerInterface   $logger
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('path', InputArgument::OPTIONAL, 'Directory to watch', $this->watchedDir);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = (string) $input->getArgument('path');
        $this->logger->info('Starting watcher command', ['path' => $path]);
        $output->writeln("Watching directory: $path");

        try {
            $this->watcher->run();
        } catch (\Throwable $e) {
            $this->logger->error('Watcher loop crashed', ['exception' => $e]);
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
