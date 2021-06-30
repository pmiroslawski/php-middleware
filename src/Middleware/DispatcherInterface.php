<?php

namespace Bit9\Middleware;

use Bit9\Middleware\Letter\Envelope;
use Bit9\Middleware\Letter\Stamp\StampInterface;

/**
 * @author Pawel Miroslawski <pmiroslawski@gmail.com>
 */
interface DispatcherInterface
{
    /**
     * Dispatches the given message.
     *
     * @param object|Envelope  $message The message or the message pre-wrapped in an envelope
     * @param StampInterface[] $stamps
     */
    public function dispatch($message, array $stamps = []): Envelope;
}
