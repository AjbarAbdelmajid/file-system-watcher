<?php
namespace App\Infrastructure\Middleware;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class MessageLoggingMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly LoggerInterface $logger) {}

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $msg = $envelope->getMessage();
        $this->logger->debug('Dispatching message', ['message' => get_class($msg)]);

        $envelope = $stack->next()->handle($envelope, $stack);

        /** @var HandledStamp|null $stamp */
        $stamp = $envelope->last(HandledStamp::class);
        $this->logger->debug('Message handled', [
            'message' => get_class($msg),
            'result'  => $stamp?->getResult(),
        ]);

        return $envelope;
    }
}
