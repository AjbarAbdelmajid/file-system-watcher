<?php
namespace App\Infrastructure\Command;

use App\Infrastructure\Adapter\InotifyWatcher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WatchCommand extends Command
{
    protected static $defaultDescription = 'Start the real-time file system watcher.';

    public function __construct(
        private readonly InotifyWatcher $watcher,
        private readonly string $watchedDir
    ) {
        parent::__construct('file:watch');
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
        $output->writeln("Watching directory: $path");
        $this->watcher->run();
        return Command::SUCCESS;
    }
}
