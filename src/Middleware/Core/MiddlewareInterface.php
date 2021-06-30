<?php

namespace Bit9\Middleware\Core;

use Bit9\Middleware\Letter\Envelope;

/**
 * @author Pawel Miroslawski <pmiroslawski@gmail.com>
 */
interface MiddlewareInterface
{
    public function handle(Envelope $envelope, MiddlewareStackInterface $stack): Envelope;
}
