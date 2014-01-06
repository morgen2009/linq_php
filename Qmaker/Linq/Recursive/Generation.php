<?php

namespace Qmaker\Linq\Recursive;

use Qmaker\Linq\Iterators\CallbackIterator;
use Qmaker\Linq\LinqExecute;
use Qmaker\Linq\Meta\Stream;
use Qmaker\Linq\WrongTypeException;

/**
 * @see \Qmaker\Linq\Operation\Generation
 */
trait Generation
{
    /**
     * @see \Qmaker\Linq\Operation\Generation::range
     */
    function range($start, $count) {
        $factory = function () use ($start, $count) {
            return function ($offset) use ($start, $count) {
                if ($offset >= $count) {
                    throw new \OutOfBoundsException();
                } else {
                    return $start + $offset;
                }
            };
        };
        $this->from($factory);
        return $this;
    }

    /**
     * @see \Qmaker\Linq\Operation\Generation::repeat
     */
    function repeat($element, $count) {
        $factory = function () use ($element, $count) {
            return function ($offset) use ($element, $count) {
                if ($offset >= $count) {
                    throw new \OutOfBoundsException();
                } else {
                    return $element;
                }
            };
        };
        $this->from($factory);
        return $this;
    }

    /**
     * @see \Qmaker\Linq\Operation\Generation::from
     */
    function from($source) {
        $this->importSource($source);
        return $this;
    }


    /**
     * @see \Qmaker\Linq\Operation\Generation::alias
     */
    public function alias($name) {
        /** @var \Qmaker\Linq\Meta\MetaAware $this */
        $this->meta->getCurrent()->setName($name);
        return $this;
    }

    /**
     * @param $source
     * @return \Qmaker\Linq\Meta\Stream
     * @throws \Qmaker\Linq\WrongTypeException
     */
    private function importSource($source) {
        /** @var \Qmaker\Linq\Meta\MetaAware $this */
        if (is_string($source)) {
            $stream = $this->meta->getStream($source);
            $this->meta->setCurrent($stream);
            return $stream;
        } else {
            $name = $this->meta->getDefaultName();
            if (is_array($source)) {
                $callback = function () use ($source) {
                    return new \ArrayIterator($source);
                };

            } elseif (is_callable($source)) {
                $callback = function () use ($source) {
                    return new CallbackIterator($source);
                };

            } elseif ($source instanceof \Iterator) {
                $callback = function () use ($source) {
                    return $source;
                };
                if ($source instanceof LinqExecute) {
                    $name = $source->getName();
                }

            } elseif ($source instanceof \IteratorAggregate) {
                $callback = function () use ($source) {
                    return $source->getIterator();
                };

            } else {
                throw new WrongTypeException($source, "array|\\Iterator|\\IteratorAggregate|callable", "The source");
            }
            $stream = new Stream($name);
            $stream->addItem($callback);
            $this->meta->addStream($stream);
            return $stream;
        }
    }
}