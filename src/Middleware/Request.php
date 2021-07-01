<?php

namespace Bit9\Middleware;


use Bit9\Middleware\Request\Stamp\StampInterface;

/**
 * @author Pawel Miroslawski <pmiroslawski@gmail.com>
 */
final class Request
{
    private \ArrayObject $stamps;
    private $message;

    /**
     * @param object           $message
     * @param StampInterface[] $stamps
     */
    public function __construct($message, array $stamps = [])
    {
        $this->stamps = new \ArrayObject();

        if (!\is_object($message)) {
            throw new \TypeError(sprintf('Invalid argument provided to "%s()": expected object but got "%s".', __METHOD__, get_debug_type($message)));
        }

        $this->message = $message;

        foreach ($stamps as $stamp) {
            $index = \get_class($stamp);

            if (!$this->stamps->offsetExists($index)) {
                $this->stamps->offsetSet($index, new \ArrayObject());
            }
            $this->stamps->offsetGet($index)->append($stamp);
        }
    }

    /**
     * @param object|Request  $message
     * @param StampInterface[] $stamps
     */
    public static function wrap($message, array $stamps = []): self
    {
        $envelope = $message instanceof self ? $message : new self($message);

        return $envelope->with(...$stamps);
    }

    /**
     * @return Request a new Envelope instance with additional stamp
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
     * @return Request a new Envelope instance without any stamps of the given class
     */
    public function withoutAll(string $stampFqcn): self
    {
        $cloned = clone $this;

        unset($cloned->stamps[$this->resolveAlias($stampFqcn)]);

        return $cloned;
    }

    /**
     * Removes all stamps that implement the given type.
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

    public function last(string $stampFqcn): ?StampInterface
    {
        return isset($this->stamps[$stampFqcn = $this->resolveAlias($stampFqcn)]) ? end($this->stamps[$stampFqcn]) : null;
    }

    /**
     * @return StampInterface[]|StampInterface[][] The stamps for the specified FQCN, or all stamps by their class name
     */
    public function all(string $stampFqcn = null): \ArrayObject
    {
        if (null !== $stampFqcn) {
            return $this->stamps[$this->resolveAlias($stampFqcn)] ?? new \ArrayObject();
        }

        return $this->stamps;
    }

    /**
     * @return object The original message contained in the envelope
     */
    public function getMessage(): object
    {
        return $this->message;
    }

    private function resolveAlias(string $fqcn): string
    {
        static $resolved;

        return $resolved[$fqcn] ?? ($resolved[$fqcn] = (new \ReflectionClass($fqcn))->getName());
    }
}
