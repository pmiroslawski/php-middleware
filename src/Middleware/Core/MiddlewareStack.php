<?php

namespace Bit9\Middleware\Core;

use Bit9\Middleware\Letter\Envelope;
use Bit9\Middleware\Stack\StackInterface;

/**
 * @author Pawel Miroslawski <pmiroslawski@gmail.com>
 */
class MiddlewareStack implements MiddlewareInterface, MiddlewareStackInterface
{
    private StackInterface $stack;
    private $offset = 0;

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

    public function handle(Envelope $envelope, MiddlewareStackInterface $stack): Envelope
    {
        return $envelope;
    }
}

