<?php

namespace Bit9\Middleware;

use Bit9\Middleware\Request\Stamp\StampInterface;

/**
 * @author Pawel Miroslawski <pmiroslawski@gmail.com>
 */
class RequestFlatStamps extends Request implements RequestFlatStampsInterface
{
    public function stampExists(string $stampFqcn) : bool
    {
        $stamps = $this->all($stampFqcn);

        return $stamps->count() > 0;
    }

    public function getStamp(string $stampFqcn) : StampInterface
    {
        $stamps = $this->all($stampFqcn);

        if ($stamps->count() == 0) {
            throw new \RuntimeException(sprintf("%s has not been found in the passed request. Current middleware must be executed after a middleware which set stamp '%s' in the request.", $stampFqcn, $stampFqcn));
        }

        return $stamps->offsetGet($stamps->count() - 1);
    }

    public function setStamp(StampInterface $stamp) : RequestInterface
    {
        return $this->withSingle($stamp);
    }
}
