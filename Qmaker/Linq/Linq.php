<?php

namespace Qmaker\Linq;

use Qmaker\Iterators\CallbackFilterIterator;
use Qmaker\Iterators\CallbackIterator;
use Qmaker\Iterators\Collections\DefaultComparer;
use Qmaker\Iterators\DistinctIterator;
use Qmaker\Iterators\ExceptIterator;
use Qmaker\Iterators\GroupingIterator;
use Qmaker\Iterators\IndexIterator;
use Qmaker\Iterators\IntersectIterator;
use Qmaker\Iterators\JoinIterator;
use Qmaker\Iterators\OuterJoinIterator;
use Qmaker\Iterators\ProductIterator;
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

    /**
     * @see \Qmaker\Linq\Operation\Quantifier::all
     */
    function all($expression)
    {
        $expression = LambdaFactory::create($expression);
        $result = true;

        $this->each(function ($value, \Iterator $iterator) use ($expression, &$result) {
            return $result = call_user_func($expression, $value, $iterator);
        });

        return $result;
    }

    /**
     * @see \Qmaker\Linq\Operation\Quantifier::any
     */
    function any($expression)
    {
        $expression = LambdaFactory::create($expression);
        $result = false;

        $this->each(function ($value, \Iterator $iterator) use ($expression, &$result) {
            return !($result = call_user_func($expression, $value, $iterator));
        });

        return $result;
    }

    /**
     * @see \Qmaker\Linq\Operation\Quantifier::contains
     */
    function contains($element, callable $comparator = null)
    {
        $result = false;

        if (empty($comparator)) {
            $this->each(function ($value) use ($element, &$result) {
                return !($result = ($value == $element));
            });
        } else {
            $this->each(function ($value) use ($element, $comparator, &$result) {
                return !($result = (call_user_func($comparator, $value, $element) === 0));
            });
        }

        return $result;
    }

    /**
     * Apply callback to each element of the sequence
     * @param callable $action if callback returns false, the iteration stops
     * @return boolean false, if the iteration breaks
     */
    public function each(callable $action) {
        $iterator = $this->getIterator();
        $iterator->rewind();

        while ($iterator->valid()) {
            if (call_user_func($action, $iterator->current(), $iterator) === false) {
                return false;
            };
            $iterator->next();
        }

        return true;
    }

    /**
     * @see \Qmaker\Linq\Operation\Joining::product
     */
    function product($source, $projector = null)
    {
        $source = $this->from($source);
        $projector = empty($projector) ? null : LambdaFactory::create($projector);

        return new Linq(function (\Iterator $iteratorA, \Iterator $iteratorB) use ($projector) {

            if ($iteratorA instanceof ProductIterator) {
                // merge with left iterator
                $iterator = $iteratorA;
                $iterator->attachIterator($iteratorB);
            } else {
                // create new product iterator
                $iterator = new ProductIterator();
                $iterator->attachIterator($iteratorA);
                $iterator->attachIterator($iteratorB);
            }

            if (empty($projector)) {
                return $iterator;
            } else {
                return new ProjectionIterator($iterator, $projector);
            }
        }, [$this, $source]);
    }

    /**
     * @see \Qmaker\Linq\Operation\Joining::join
     */
    function join($source, $expression, $expressionInner, $projector = null, $predicate = null)
    {
        $source = $this->from($source);
        $expression = LambdaFactory::create($expression);
        $expressionInner = LambdaFactory::create($expressionInner);
        $projector = empty($projector) ? $projector : LambdaFactory::create($projector);
        $predicate = empty($predicate) ? $predicate : LambdaFactory::create($predicate);

        return new Linq(function (\Iterator $iteratorA, \Iterator $iteratorB) use ($expression, $expressionInner, $projector, $predicate) {

            $iteratorB = new IndexIterator($iteratorB, $expression);
            $iterator = new JoinIterator($iteratorA, $expressionInner, $iteratorB);

            if (!empty($predicate)) {
                $iterator = new CallbackFilterIterator($iterator, $predicate);
            }

            if (empty($projector)) {
                return $iterator;
            } else {
                return new ProjectionIterator($iterator, $projector);
            }
        }, [$this, $source]);
    }

    /**
     * @see \Qmaker\Linq\Operation\Joining::joinOuter
     */
    function joinOuter($source, $expression, $expressionInner, $projector = null, $predicate = null)
    {
        $source = $this->from($source);
        $expression = LambdaFactory::create($expression);
        $expressionInner = LambdaFactory::create($expressionInner);
        $projector = empty($projector) ? : LambdaFactory::create($projector);
        $predicate = empty($predicate) ? : LambdaFactory::create($predicate);

        return new Linq(function (\Iterator $iteratorA, \Iterator $iteratorB) use ($expression, $expressionInner, $projector, $predicate) {

            $iteratorB = new IndexIterator($iteratorB, $expression);
            $iterator = new OuterJoinIterator($iteratorA, $expressionInner, $iteratorB);

            if (!empty($predicate)) {
                $iterator = new CallbackFilterIterator($iterator, $predicate);
            }

            if (empty($projector)) {
                return $iterator;
            } else {
                return new ProjectionIterator($iterator, $projector);
            }
        }, [$this, $source]);
    }

    /**
     * @see \Qmaker\Linq\Operation\Joining::groupJoin
     */
    function groupJoin($source, $expression, $expressionInner, $projector = null, $predicate = null)
    {
        $self = $this->join($source, $expression, $expressionInner, $projector, $predicate);

        return new Linq(function (\Iterator $iterator) {
            return new GroupingIterator($iterator);
        }, [$self]);
    }

    /**
     * @see \Qmaker\Linq\Operation\Aggregation::aggregate
     */
    function aggregate(callable $accumulate, callable $init = null)
    {
        $value = empty($init) ? 0 : call_user_func($init);
        foreach ($this as $item) {
            $value = call_user_func($accumulate, $item, $value);
        }
        return $value;
    }

    /**
     * @see \Qmaker\Linq\Operation\Aggregation::average
     */
    function average($expression = null)
    {
        $sum = 0;
        $count = 0;

        if (!empty($expression)) {
            $expression = LambdaFactory::create($expression);
            foreach ($this as $item) {
                $sum += call_user_func($expression, $item);
                $count++;
            };
        } else {
            foreach ($this as $item) {
                $sum += $item;
                $count++;
            };
        }

        return $count > 0 ? $sum / $count : 0;
    }

    /**
     * @see \Qmaker\Linq\Operation\Aggregation::count
     */
    function count($expression = null)
    {
        if (empty($expression)) {
            return iterator_count($this);
        } else {
            $expression = LambdaFactory::create($expression);
            $count = 0;
            foreach ($this as $item) {
                $item = call_user_func($expression, $item);
                if (!empty($item)) {
                    $count++;
                }
            };
            return $count;
        }
    }

    /**
     * @see \Qmaker\Linq\Operation\Aggregation::max
     */
    function max($expression = null)
    {
        $max = null;

        if (!empty($expression)) {
            $expression = LambdaFactory::create($expression);
            foreach ($this as $item) {
                $item = call_user_func($expression, $item);
                if (is_null($max) || $max < $item) {
                    $max = $item;
                }
            };
        } else {
            foreach ($this as $item) {
                if (is_null($max) || $max < $item) {
                    $max = $item;
                }
            };
        }

        return $max;
    }

    /**
     * @see \Qmaker\Linq\Operation\Aggregation::min
     */
    function min($expression = null)
    {
        $min = null;

        if (!empty($expression)) {
            $expression = LambdaFactory::create($expression);
            foreach ($this as $item) {
                $item = call_user_func($expression, $item);
                if (is_null($min) || $min > $item) {
                    $min = $item;
                }
            };
        } else {
            foreach ($this as $item) {
                if (is_null($min) || $min > $item) {
                    $min = $item;
                }
            };
        }

        return $min;
    }

    /**
     * @see \Qmaker\Linq\Operation\Aggregation::sum
     */
    function sum($expression = null)
    {
        $sum = 0;

        if (!empty($expression)) {
            $expression = LambdaFactory::create($expression);
            foreach ($this as $item) {
                $sum += call_user_func($expression, $item);
            };
        } else {
            foreach ($this as $item) {
                $sum += $item;
            };
        }

        return $sum;
    }
}