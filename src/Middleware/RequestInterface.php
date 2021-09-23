<?php

namespace Bit9\Middleware;

use Bit9\Middleware\Request\Stamp\StampInterface;

/**
 * RequestFlatStampsInterface is an interface which provides basic interface for request objects
 */
interface RequestInterface
{
    /**
     * Get request data (without any stamps)
     *
     * @return mixed
     */
    public function getRequest();

    /**
     * Set stamps in the request
     *
     * If more stamps of given type are passing all of them are store in the request.
     *
     * @param StampInterface ...$stamps
     * @return RequestInterface
     */
    public function with(StampInterface ...$stamps): RequestInterface;

    /**
     *
     * @param string $stampFqcn
     * @return RequestInterface
     */
    public function withoutAll(string $stampFqcn): RequestInterface;

    /**
     *
     * @param string $type
     * @return self
     */
    public function withoutStampsOfType(string $type): self;

    /**
     * Get all stamps
     *
     * @param string $stampFqcn
     * @return \ArrayObject
     */
    public function all(string $stampFqcn = null): \ArrayObject;
}