<?php

namespace Qmaker\Linq;

use Qmaker\Iterators\CallbackFilterIterator;
use Qmaker\Iterators\CallbackIterator;
use Qmaker\Iterators\Collections\DefaultComparer;
use Qmaker\Iterators\DistinctIterator;
use Qmaker\Iterators\ExceptIterator;
use Qmaker\Iterators\IndexIterator;
use Qmaker\Iterators\IntersectIterator;
use Qmaker\Iterators\ProjectionIterator;
use Qmaker\Iterators\ReverseIterator;
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
     * @var array
     */
    protected $info;

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

    /**
     * @see \Qmaker\Linq\Operation\Projection::select
     */
    function select($expression)
    {
        $expression = LambdaFactory::create($expression);

        return new Linq(function (\Iterator $iterator) use ($expression) {
            return new ProjectionIterator($iterator, $expression);
        }, [$this]);
    }

    /**
     * @see \Qmaker\Linq\Operation\Projection::selectMany
     */
    function selectMany($expression)
    {
        $expression = LambdaFactory::create($expression);

        return new Linq(function (\Iterator $iterator) use ($expression) {
            return new \RecursiveIteratorIterator(new ProjectionIterator($iterator, $expression), \RecursiveIteratorIterator::CHILD_FIRST);
        }, [$this]);
    }

    /**
     * @see \Qmaker\Linq\Operation\Projection::cast
     */
    function cast($name)
    {
        return new Linq(function (\Iterator $iterator) use ($name) {
            return new ProjectionIterator($iterator, function ($item) use ($name) {
                settype($item, $name);
                return $item;
            });
        }, [$this]);
    }

    /**
     * @see \Qmaker\Linq\Operation\Set::distinct
     */
    function distinct($expression, callable $comparator = null)
    {
        $expression = LambdaFactory::create($expression);

        return new Linq(function (\Iterator $iterator) use ($expression) {
            return new DistinctIterator($iterator, $expression);
        }, [$this]);
    }

    /**
     * @see \Qmaker\Linq\Operation\Set::except
     */
    function except($sequence, $expression, callable $comparator = null)
    {
        $expression = LambdaFactory::create($expression);
        $sequence = $this->from($sequence);

        return new Linq(function (\Iterator $iterator, \Iterator $iteratorSub) use ($expression) {
            return new ExceptIterator($iterator, $iteratorSub, $expression);
        }, [$this, $sequence]);
    }

    /**
     * @see \Qmaker\Linq\Operation\Set::intersect
     */
    function intersect($sequence, $expression, callable $comparator = null)
    {
        $expression = LambdaFactory::create($expression);
        $sequence = $this->from($sequence);

        return new Linq(function (\Iterator $iteratorA, \Iterator $iteratorB) use ($expression) {
            return new IntersectIterator($iteratorA, $iteratorB, $expression);
        }, [$this, $sequence]);
    }

    /**
     * @see \Qmaker\Linq\Operation\Set::union
     */
    function union($sequence, $expression, callable $comparator = null)
    {
        $expression = LambdaFactory::create($expression);
        $sequence = $this->from($sequence);

        return new Linq(function (\Iterator $iteratorA, \Iterator $iteratorB) use ($expression) {
            $iterator = new \AppendIterator();
            $iterator->append($iteratorA);
            $iterator->append($iteratorB);
            return new DistinctIterator($iterator, $expression);
        }, [$this, $sequence]);
    }

    /**
     * @see \Qmaker\Linq\Operation\Sorting::orderBy
     */
    function orderBy($expression, callable $comparator = null)
    {
        return $this->indexAdd($expression, $comparator, true);
    }

    /**
     * @see \Qmaker\Linq\Operation\Sorting::orderByDescending
     */
    function orderByDescending($expression, callable $comparator = null)
    {
        return $this->indexAdd($expression, $comparator, false);
    }

    /**
     * @see \Qmaker\Linq\Operation\Sorting::thenBy
     */
    function thenBy($expression, callable $comparator = null)
    {
        return $this->indexAdd($expression, $comparator, true);
    }

    /**
     * @see \Qmaker\Linq\Operation\Sorting::thenByDescending
     */
    function thenByDescending($expression, callable $comparator = null)
    {
        return $this->indexAdd($expression, $comparator, false);
    }

    /**
     * @param mixed $expression
     * @param callable $comparator
     * @param boolean $ascending
     * @return IEnumerable
     */
    protected function indexAdd($expression, callable $comparator = null, $ascending) {
        if (empty($this->info) || array_key_exists('index', $this->info) === false) {
            $self = new Linq(function () { return null; }, [$this]);
            $self->info['index'] = true;
            $self->init = function (\Iterator $iterator) use ($self) {
                if (count($self->info['index_exp']) > 1) {
                    // build key extractor
                    $keyExtractor = LambdaFactory::create($self->info['index_exp']);

                    // build comparator
                    $info = $self->info['index_cmp'];
                    $comparator = function ($x, $y) use ($info) {
                        foreach ($info as $key => $comparator) {
                            if (empty($comparator[0])) {
                                $res = DefaultComparer::compare($x[$key], $y[$key]);
                            } else {
                                $res = call_user_func($comparator[0], $x[$key], $y[$key]);
                            }
                            if ($res != 0) {
                                return $comparator[1] ? $res : -$res;
                            }
                        }
                        return 0;
                    };
                } else {
                    // build key extractor
                    $keyExtractor = LambdaFactory::create($self->info['index_exp'][0]);

                    // build comparator
                    $info = $self->info['index_cmp'][0];
                    $comparator = function ($x, $y) use ($info) {
                        if (empty($info[0])) {
                            $res = DefaultComparer::compare($x, $y);
                        } else {
                            $res = call_user_func($info[0], $x, $y);
                        }
                        if ($res != 0) {
                            return $info[1] ? $res : -$res;
                        }
                        return 0;
                    };
                }
                return new IndexIterator($iterator, $keyExtractor, $comparator);
            };
        } else {
            $self = $this;
        }

        $self->info['index_exp'][] = $expression;
        $self->info['index_cmp'][] = [$comparator, $ascending];

        return $self;
    }

    /**
     * @see \Qmaker\Linq\Operation\Sorting::reverse
     */
    function reverse()
    {
        return new Linq(function (\Iterator $iterator) {
            return new ReverseIterator($iterator);
        }, [$this]);
    }

    /**
     * @see \Qmaker\Linq\Operation\Sorting::order
     */
    function order(callable $comparator = null)
    {
        return new Linq(function (\Iterator $iterator) use ($comparator) {
            return new IndexIterator($iterator, function ($value) {
                return $value;
            }, $comparator);
        }, [$this]);
    }
}
