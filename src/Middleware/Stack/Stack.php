<?php

namespace Bit9\Middleware\Stack;

use Bit9\Middleware\Core\MiddlewareInterface;

/**
 * Simple stack implementation
 *
 * @author Pawel Miroslawski <pmiroslawski@gmail.com>
 */
class Stack implements StackInterface
{
    private ?\Iterator $iterator;
    private array $stack = [];

    public function setIterator(\Iterator $iterator) : void
    {
        $this->iterator = $iterator;
    }

    public function append(MiddlewareInterface $middleware) : void
    {
        $this->stack[] = $middleware;
    }

    public function next(int $offset): ?MiddlewareInterface
    {
        if (isset($this->stack[$offset])) {
            return $this->stack[$offset];
        }

        if (null === $this->iterator) {
            return null;
        }

        $this->iterator->next();

        if (!$this->iterator->valid()) {
            return $this->iterator = null;
        }

        return $this->stack[] = $this->iterator->current();
    }
}