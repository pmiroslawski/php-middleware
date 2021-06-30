<?php

namespace Bit9\Middleware\Stack;

use Bit9\Middleware\Core\MiddlewareInterface;

/**
 * @author Pawel Miroslawski <pmiroslawski@gmail.com>
 */
interface StackInterface
{
    /**
     * Sets a stack iterator
     */
    public function setIterator(\Iterator $iterator) : void;

    /**
     * Appends stack element
     */
    public function append(MiddlewareInterface $middleware) : void;

    /**
     * Returns the next middleware to process a message.
     */
    public function next(int $offset): ?MiddlewareInterface;
}
