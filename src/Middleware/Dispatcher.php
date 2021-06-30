<?php

namespace Bit9\Middleware;

use Bit9\Middleware\Core\MiddlewareStack;
use Bit9\Middleware\Letter\Envelope;
use Bit9\Middleware\Stack\Stack;

/**
 * @author Pawel Miroslawski <pmiroslawski@gmail.com>
 */
class Dispatcher implements DispatcherInterface
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
    public function dispatch($message, array $stamps = []): Envelope
    {
        if (!\is_object($message)) {
            throw new \TypeError(sprintf('Invalid argument provided to "%s()": expected object, but got "%s".', __METHOD__, get_debug_type($message)));
        }

        $envelope = Envelope::wrap($message, $stamps);
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
}
