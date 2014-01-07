<?php

namespace Qmaker\Linq;

use Qmaker\Iterators\CallbackFilterIterator;
use Qmaker\Iterators\CallbackIterator;
use Qmaker\Iterators\SkipIterator;
use Qmaker\Iterators\TakeIterator;
use Qmaker\Linq\Expression\LambdaFactory;

class Linq implements IEnumerable
{
    /**
     * @var callable
     */
    protected $init;

    /**
     * @var IEnumerable[]
     */
    protected $parent;

    /**
     * Constructor
     * @param callable $init
     * @param IEnumerable[] $parent
     */
    protected function __construct(callable $init, array $parent = null) {
        $this->parent = empty($parent) ? [] : $parent;
        $this->init = $init;
    }

    /**
     * @see \IteratorAggregate::getIterator
     */
    public function getIterator() {
        $parent = array_map(function (IEnumerable $item) {
            return $item->getIterator();
        }, $this->parent);

        return call_user_func_array($this->init, $parent);
    }

    public function toArray() {
        return iterator_to_array($this->getIterator(), false);
    }

    /**
     * @see \Qmaker\Linq\Operation\Generation::range
     */
    static function range($start, $count)
    {
        return Linq::from(function () use ($start, $count) {
            $current = $start;
            $max = $start + $count;
            return function () use (&$current, $max) {
                if ($current >= $max) {
                    throw new \OutOfBoundsException();
                }
                return $current++;
            };
        });
    }

    /**
     * @see \Qmaker\Linq\Operation\Generation::repeat
     */
    static function repeat($element, $count)
    {
        return Linq::from(function () use ($element, $count) {
            return function (\Iterator $iterator) use ($element, $count) {
                if ($iterator->key() >= $count) {
                    throw new \OutOfBoundsException();
                }
                return $element;
            };
        });
    }

    /**
     * @see \Qmaker\Linq\Operation\Generation::from
     */
    static function from($source)
    {
        return new Linq(function () use ($source) {
            if (is_array($source)) {
                return new \ArrayIterator($source);

            } elseif (is_callable($source)) {
                return new CallbackIterator($source);

            } elseif ($source instanceof \Iterator) {
                return $source;

            } elseif ($source instanceof \IteratorAggregate) {
                return $source->getIterator();

            } else {
                return new \ArrayIterator([$source]);
            }
        });
    }

    /**
     * @see \Qmaker\Linq\Operation\Filtering::ofType
     */
    function ofType($name)
    {
        if (class_exists($name, true) || trait_exists($name, true)) {
            $predicate = function ($item) use ($name) {
                return $item instanceof $name;
            };
        } elseif (function_exists('is_' . $name)) {
            $predicate = function ($item) use ($name) {
                return call_user_func('is_' . $name, $item);
            };
        } else {
            throw new WrongTypeException($name, 'class name|trait name|type name');
        }

        return $this->where($predicate);
    }

    /**
     * @see \Qmaker\Linq\Operation\Filtering::where
     */
    function where($predicate)
    {
        $predicate = LambdaFactory::create($predicate);

        return new Linq(function (\Iterator $iterator) use ($predicate) {
            if ($iterator instanceof CallbackFilterIterator) {
                $iterator->addCallback($predicate);
                return $iterator;
            } else {
                return new CallbackFilterIterator($iterator, $predicate);
            }
        }, [$this]);
    }

    /**
     * @see \Qmaker\Linq\Operation\Partitioning::skip
     */
    function skip($count)
    {
        return new Linq(function (\Iterator $iterator) use ($count) {
            return new \LimitIterator($iterator, $count);
        }, [$this]);
    }

    /**
     * @see \Qmaker\Linq\Operation\Partitioning::skipWhile
     */
    function skipWhile($predicate)
    {
        $predicate = LambdaFactory::create($predicate);

        return new Linq(function (\Iterator $iterator) use ($predicate) {
            return new SkipIterator($iterator, $predicate);
        }, [$this]);
    }

    /**
     * @see \Qmaker\Linq\Operation\Partitioning::take
     */
    function take($count)
    {
        return new Linq(function (\Iterator $iterator) use ($count) {
            return new \LimitIterator($iterator, 0, $count);
        }, [$this]);
    }

    /**
     * @see \Qmaker\Linq\Operation\Partitioning::takeWhile
     */
    function takeWhile($predicate)
    {
        $predicate = LambdaFactory::create($predicate);

        return new Linq(function (\Iterator $iterator) use ($predicate) {
            return new TakeIterator($iterator, $predicate);
        }, [$this]);
    }
}
