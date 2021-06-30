<?php

namespace Bit9\Middleware;

use Bit9\Middleware\Letter\Envelope;

/**
 * @author Pawel Miroslawski <pmiroslawski@gmail.com>
 */
interface DispatcherInterface
{
    public function dispatch(Envelope $envelope): Envelope;
}