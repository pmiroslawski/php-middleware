<?php

namespace Bit9\Middleware\Core;

use Bit9\Middleware\MiddlewareInterface;

/**
 * @author Pawel Miroslawski <pmiroslawski@gmail.com>
 */
interface MiddlewareStackInterface
{
    /**
     * Returns the next middleware to process a message.
     */
    public function next(): MiddlewareInterface;
}
