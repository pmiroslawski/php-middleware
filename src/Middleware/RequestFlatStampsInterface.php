<?php

namespace Bit9\Middleware;

use Bit9\Middleware\Request\Stamp\StampInterface;

/**
 * RequestFlatStampsInterface is an interface which provides more readable interface for request objects
 *
 * The main difference between this interface and RequestStampInterface is the fact, that
 * RequestFlatStampsInterface assumes existing only one stamp of given type in given request.
 */
interface RequestFlatStampsInterface
{
    /**
     * Check if stamp of given type exists in the request
     */
    public function stampExists(string $stampFqcn) : bool;

    /**
     * Get the stamp of given type from the request
     */
    public function getStamp(string $stampFqcn) : StampInterface;

    /**
     * Set the stamp of given type in the request (or overwrite existing one if needed)
     */
    public function setStamp(StampInterface $stamp) : RequestInterface;
}