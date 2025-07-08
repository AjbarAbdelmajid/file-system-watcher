<?php
namespace App\Infrastructure\MessageHandler;

use App\Core\Application\UseCase\HandleFileEvent;
use App\Core\Application\Message\ProcessFileEventMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Psr\Log\LoggerInterface;

#[AsMessageHandler]
final class ProcessFileEventHandler
{
    public function __construct(
        private readonly HandleFileEvent $useCase,
        private readonly LoggerInterface  $logger
    ) {}

    public function __invoke(ProcessFileEventMessage $message): void
    {
        $this->useCase->process($message->path, $message->eventType);
    }
}
