<?php

namespace Bit9\Middleware;

use Bit9\Middleware\Stack\Stack;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * @author Pawel Miroslawski <pmiroslawski@gmail.com>
 */
class MiddlewareTraceable extends Middleware
{
    private ?Stopwatch $stopwatch = null;

    public function setStopwatch(Stopwatch $stopwatch)
    {
        $this->stopwatch = $stopwatch;
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

        $middlewareStack = new StackTraceable($stack, $middlewareIterator, $this->stopwatch);
        if ($this->stopwatch) {
            $middlewareStack->setStopwatch($this->stopwatch);
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

}
