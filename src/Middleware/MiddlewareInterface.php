<?php

namespace Bit9\Middleware;

use Bit9\Middleware\Letter\Envelope;
use Bit9\Middleware\Core\MiddlewareStackInterface;

/**
 * @author Pawel Miroslawski <pmiroslawski@gmail.com>
 */
interface MiddlewareInterface
{
    public function handle(Envelope $envelope, ?MiddlewareStackInterface $stack = null): Envelope;
}
