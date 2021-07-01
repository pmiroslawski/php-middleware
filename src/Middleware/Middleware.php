<?php

namespace Bit9\Middleware;

use Bit9\Middleware\Stack\Stack;

/**
 * @author Pawel Miroslawski <pmiroslawski@gmail.com>
 */
class Middleware implements MiddlewareInterface
{
    protected \IteratorAggregate $middlewareAggregate;

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
    protected function dispatch(Request $request): Request
    {
        $middlewareIterator = $this->middlewareAggregate->getIterator();
        while ($middlewareIterator instanceof \IteratorAggregate) {
            $middlewareIterator = $middlewareIterator->getIterator();
        }

        $middlewareIterator->rewind();

        if (!$middlewareIterator->valid()) {
            return $request;
        }

        $stack = new Stack();

        $middlewareStack = new StackMiddleware($stack, $middlewareIterator);

        return $middlewareIterator->current()->handle($request, $middlewareStack);
    }

    public function handle(Request $request, ?MiddlewareStackInterface $stack = null): Request
    {
        if ($this->middlewareAggregate->count()) {
            $request = $this->dispatch($request);
            if ($stack == null) {
                return $request;
            }
        }

        return $stack->next()->handle($request, $stack);
    }
}
