<?php

namespace Bit9\Middleware;

use Bit9\Middleware\Stack\StackInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class StackTraceable implements MiddlewareStackInterface, MiddlewareInterface
{
    private StackInterface $stack;
    private int $offset = 0;

    private ?Stopwatch $stopwatch = null;
    private string $eventCategory = '';

    private ?string $currentEvent = null;

    public function __construct(StackInterface $stack, $middlewareIterator = null)
    {
        $this->stack = $stack;

        if (null === $middlewareIterator) {
            return;
        }

        if ($middlewareIterator instanceof \Iterator) {
            $this->stack->setIterator($middlewareIterator);
        } elseif ($middlewareIterator instanceof MiddlewareInterface) {
            $this->stack->append($middlewareIterator);
        } else {
            throw new \TypeError(sprintf('Argument 1 passed to "%s()" must be iterable of "%s" or "%s", "%s" given.', __METHOD__, MiddlewareInterface::class, \Iterator::class, get_debug_type($middlewareIterator)));
        }
    }

    public function setStopwatch(Stopwatch $stopwatch)
    {
        $this->stopwatch = $stopwatch;
    }

    /**
     * {@inheritdoc}
     */
    public function next(): MiddlewareInterface
    {
        $this->stop();

        if (null === $nextMiddleware = $this->stack->next($this->offset)) {
            return $this;
        }

        ++$this->offset;

        $this->start($nextMiddleware);

        return $nextMiddleware;
    }

    public function start($nextMiddleware): void
    {
        if ($this->stopwatch === null) {
            return;
        }

        $this->currentEvent = sprintf('"%s"', get_debug_type($nextMiddleware));

        $this->stopwatch->start($this->currentEvent, $this->eventCategory);
    }

    public function stop(): void
    {
        if ($this->stopwatch === null) {
            return;
        }

        if (null !== $this->currentEvent && $this->stopwatch->isStarted($this->currentEvent)) {
            $this->stopwatch->stop($this->currentEvent);
            $this->currentEvent = null;
        }
    }

    public function handle(Request $request, ?MiddlewareStackInterface $stack = null): Request
    {
        return $request;
    }
}