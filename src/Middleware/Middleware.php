<?php

namespace Bit9\Middleware;

use Bit9\Middleware\Stack\Stack;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * @author Pawel Miroslawski <pmiroslawski@gmail.com>
 */
class Middleware implements MiddlewareInterface
{
    protected \IteratorAggregate $middlewareAggregate;

    private ?Stopwatch $stopwatch = null;
    private ?string $eventCategory;

    public function __construct(iterable $middlewareHandlers = [])
    {
        if (\is_array($middlewareHandlers)) {
            $this->middlewareAggregate = new \ArrayObject($middlewareHandlers);
        }

        if ($middlewareHandlers instanceof \IteratorAggregate) {
            $this->middlewareAggregate = $middlewareHandlers;
        }
    }

    public function setStopwatch(Stopwatch $stopwatch, string $eventCategory) : MiddlewareInterface
    {
        $this->stopwatch = $stopwatch;
        $this->eventCategory = $eventCategory;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function dispatch(RequestInterface $request): RequestInterface
    {
        $middlewareIterator = $this->middlewareAggregate->getIterator();
        while ($middlewareIterator instanceof \IteratorAggregate) {
            $middlewareIterator = $middlewareIterator->getIterator();
        }

        $middlewareIterator->rewind();

        if (!$middlewareIterator->valid()) {
            return $request;
        }

        $middlewareStack = new StackMiddleware(new Stack(), $middlewareIterator);
        if ($this->stopwatch) {
            $middlewareStack->setStopwatch($this->stopwatch, $this->eventCategory);
        }

        try {
            $middleware = $middlewareIterator->current();

            $middlewareStack->start($middleware);

            return $middleware->handle($request, $middlewareStack);
        }
        finally {
            $middlewareStack->stop();
        }
    }

    public function handle(RequestInterface $request, ?MiddlewareStackInterface $stack = null): RequestInterface
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
