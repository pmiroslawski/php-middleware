<?php

namespace Bit9\Middleware;


use Bit9\Middleware\Stack\StackInterface;

/**
 * @author Pawel Miroslawski <pmiroslawski@gmail.com>
 */
class StackMiddleware implements MiddlewareStackInterface, MiddlewareInterface
{
    private StackInterface $stack;
    private int $offset = 0;

    /**
     * @param iterable|MiddlewareInterface[]|MiddlewareInterface|null $middlewareIterator
     */
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

    public function next(): MiddlewareInterface
    {
        if (null === $next = $this->stack->next($this->offset)) {
            return $this;
        }

        ++$this->offset;

        return $next;
    }

    public function handle(Request $request, ?MiddlewareStackInterface $stack = null): Request
    {
        return $request;
    }
}

