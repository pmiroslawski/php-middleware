<?php

namespace Bit9\Middleware;

use Bit9\Middleware\Request\Stamp\StampInterface;

/**
 * @author Pawel Miroslawski <pmiroslawski@gmail.com>
 */
class Request implements RequestInterface
{
    protected \ArrayObject $stamps;
    private $request;

    /**
     * Create Request object
     *
     * @param mixed            $request
     * @param StampInterface[] $stamps
     */
    public function __construct($request, array $stamps = [])
    {
        $this->stamps = new \ArrayObject();

        $this->request = $request;

        foreach ($stamps as $stamp) {
            $index = \get_class($stamp);
            if (!$this->stamps->offsetExists($index)) {
                $this->stamps->offsetSet($index, new \ArrayObject());
            }

            $this->stamps->offsetGet($index)->append($stamp);
        }
    }

    /**
     * @param mixed            $request
     * @param StampInterface[] $stamps
     */
    public static function wrap($request, array $stamps = []): self
    {
        $envelope = $request instanceof self ? $request : new self($request);

        return $envelope->with(...$stamps);
    }

    /**
     * Get a new (cloned) request object instance with additional stamps
     *
     * {@inheritDoc}
     * @see \Bit9\Middleware\RequestInterface::with()
     *
     * @return Request
     */
    public function with(StampInterface ...$stamps): self
    {
        $cloned = clone $this;

        foreach ($stamps as $stamp) {
            $cloned->stamps[\get_class($stamp)][] = $stamp;
        }

        return $cloned;
    }

    /**
     * Request a new request instance without any stamps of the given type
     *
     * {@inheritDoc}
     * @see \Bit9\Middleware\RequestInterface::withoutAll()
     *
     * @return Request
     */
    public function withoutAll(string $stampFqcn): self
    {
        $cloned = clone $this;

        unset($cloned->stamps[$this->resolveAlias($stampFqcn)]);

        return $cloned;
    }

    /**
     * Removes all stamps that implement the given type.
     *
     * {@inheritDoc}
     * @see \Bit9\Middleware\RequestInterface::withoutStampsOfType()
     *
     * @return Request
     */
    public function withoutStampsOfType(string $type): self
    {
        $cloned = clone $this;
        $type = $this->resolveAlias($type);

        foreach ($cloned->stamps as $class => $stamps) {
            if ($class === $type || is_subclass_of($class, $type)) {
                unset($cloned->stamps[$class]);
            }
        }

        return $cloned;
    }

    /**
     * {@inheritDoc}
     * @see \Bit9\Middleware\RequestInterface::last()
     */
    public function last(string $stampFqcn): ?StampInterface
    {
        return isset($this->stamps[$stampFqcn = $this->resolveAlias($stampFqcn)]) ? end($this->stamps[$stampFqcn]) : null;
    }

    /**
     * {@inheritDoc}
     * @see \Bit9\Middleware\RequestInterface::all()
     */
    public function all(string $stampFqcn = null): \ArrayObject
    {
        if (null !== $stampFqcn) {
            return $this->stamps[$this->resolveAlias($stampFqcn)] ?? new \ArrayObject();
        }

        return $this->stamps;
    }

    /**
     * The original request data contained in the request object
     *
     * {@inheritDoc}
     * @see \Bit9\Middleware\RequestInterface::getRequest()
     *
     * @return mixed
     */
    public function getRequest()
    {
        return $this->request;
    }

    private function resolveAlias(string $fqcn): string
    {
        static $resolved;

        return $resolved[$fqcn] ?? ($resolved[$fqcn] = (new \ReflectionClass($fqcn))->getName());
    }
}
