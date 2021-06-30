<?php

namespace Bit9\Middleware;

use Bit9\Middleware\Core\MiddlewareStack;
use Bit9\Middleware\Letter\Envelope;
use Bit9\Middleware\Stack\Stack;
use Bit9\Middleware\Core\MiddlewareStackInterface;

/**
 * @author Pawel Miroslawski <pmiroslawski@gmail.com>
 */
class Middleware implements MiddlewareInterface, DispatcherInterface
{
    private \IteratorAggregate $middlewareAggregate;

    public function __construct(iterable $middlewareHandlers = [])
    {
        if (\is_array($middlewareHandlers)) {
            $this->middlewareAggregate = new \ArrayObject($middlewareHandlers);
        }

        if ($middlewareHandlers instanceof \IteratorAggregate) {
            $this->middlewareAggregate = $middlewareHandlers;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(Envelope $envelope): Envelope
    {
        $middlewareIterator = $this->middlewareAggregate->getIterator();
        while ($middlewareIterator instanceof \IteratorAggregate) {
            $middlewareIterator = $middlewareIterator->getIterator();
        }

        $middlewareIterator->rewind();

        if (!$middlewareIterator->valid()) {
            return $envelope;
        }

        $stack = new Stack();

        $middlewareStack = new MiddlewareStack($stack, $middlewareIterator);

        return $middlewareIterator->current()->handle($envelope, $middlewareStack);
    }

    public function handle(Envelope $envelope, ?MiddlewareStackInterface $stack = null): Envelope
    {
        if ($this->middlewareAggregate->count()) {
            $envelope = $this->dispatch($envelope);
            if ($stack == null) {
                return $envelope;
            }
        }

        dump(get_class($this));

        return $stack->next()->handle($envelope, $stack);
    }
}