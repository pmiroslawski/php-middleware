<?php

namespace Bit9\Middleware;

/**
 * @author Pawel Miroslawski <pmiroslawski@gmail.com>
 */
interface MiddlewareInterface
{
    public function handle(Request $request, ?MiddlewareStackInterface $stack = null): Request;
}
