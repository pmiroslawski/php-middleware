<?php

namespace Bit9\Middleware;

use Bit9\Middleware\Request\Stamp\StampInterface;

interface RequestFlatStampsInterface
{
    public function stampExists(string $stampFqcn) : bool;
    public function getStamp(string $stampFqcn) : StampInterface;
    public function setStamp(StampInterface $stamp) : RequestInterface;
}