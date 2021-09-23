<?php

namespace Bit9\Middleware;

use Bit9\Middleware\Request\Stamp\StampInterface;

/**
 * @author Pawel Miroslawski <pmiroslawski@gmail.com>
 */
class RequestFlatStamps extends Request implements RequestFlatStampsInterface
{
    /**
     * {@inheritDoc}
     * @see \Bit9\Middleware\RequestFlatStampsInterface::stampExists()
     */
    public function stampExists(string $stampFqcn) : bool
    {
        $stamps = $this->all($stampFqcn);

        return $stamps->count() > 0;
    }

    /**
     * {@inheritDoc}
     * @see \Bit9\Middleware\RequestFlatStampsInterface::getStamp()
     */
    public function getStamp(string $stampFqcn) : StampInterface
    {
        $stamps = $this->all($stampFqcn);

        if ($stamps->count() == 0) {
            throw new \RuntimeException(sprintf("%s has not been found in the passed request. Current middleware must be executed after a middleware which set stamp '%s' in the request.", $stampFqcn, $stampFqcn));
        }

        return $stamps->offsetGet($stamps->count() - 1);
    }

    /**
     * Set stamps in the request but keep only one instance of the stamp of given type (the newest one)
     *
     * {@inheritDoc}
     * @see \Bit9\Middleware\RequestFlatStampsInterface::setStamp()
     */
    public function setStamp(StampInterface $stamp) : RequestInterface
    {
        return $this->with($stamp);
    }

    /**
     * Get a new (cloned) request object instance with additional stamps (but keep only one instance of given stamp type)
     *
     * {@inheritDoc}
     * @see \Bit9\Middleware\RequestInterface::withSingle()
     *
     * @return Request
     */
    public function with(StampInterface ...$stamps): self
    {
        $cloned = clone $this;

        foreach ($stamps as $stamp) {
            $index = \get_class($stamp);

            if ($cloned->stamps->offsetExists($index)) {
                $cloned->stamps->offsetSet($index, new \ArrayObject());
            }

            $cloned->stamps->offsetGet($index)->append($stamp);
        }

        return $cloned;
    }
}
