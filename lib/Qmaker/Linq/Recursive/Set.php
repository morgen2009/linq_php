<?php

namespace Qmaker\Linq\Recursive;

use Qmaker\Linq\Expression\Exp;
use Qmaker\Linq\Iterators\DistinctIterator;
use Qmaker\Linq\Iterators\ExceptIterator;
use Qmaker\Linq\Iterators\IntersectIterator;
use Qmaker\Linq\Iterators\Key\SingleKey;
use Qmaker\Linq\WrongTypeException;

/**
 * @see \Qmaker\Linq\Operation\Set
 */
trait Set
{
    /**
     * @see \Qmaker\Linq\Operation\Set::distinct
     */
    function distinct($expression = null, callable $comparator = null) {
        /** @var callable $expression */
        $expression = Exp::instanceFrom($expression);

        $element = function (\Iterator $iterator) use ($expression, $comparator) {
                $result = new DistinctIterator($iterator);
                $result->getIndex()->addKey(new SingleKey($expression, $comparator));
                return $result;
        };

        /** @var $this \Qmaker\Linq\Meta\MetaAware */
        $this->meta->getCurrent()->addItem($element);
        return $this;
    }

    /**
     * @see \Qmaker\Linq\Operation\Set::except
     */
    function except($sequence, $expression = null, callable $comparator = null) {
        if (is_array($sequence)) {
            $sequence = new \ArrayIterator($sequence);
        } elseif (!($sequence instanceof \Iterator)) {
            throw new WrongTypeException($sequence, 'array|\Iterator', 'The sequence');
        }
        /** @var callable $expression */
        $expression = Exp::instanceFrom($expression);

        $element = function (\Iterator $iterator) use ($sequence, $expression, $comparator) {
                $result = new ExceptIterator($iterator, $sequence);
                $result->getIndex()->addKey(new SingleKey($expression, $comparator));
                return $result;
        };

        /** @var $this \Qmaker\Linq\Meta\MetaAware */
        $this->meta->getCurrent()->addItem($element);
        return $this;
    }

    /**
     * @see \Qmaker\Linq\Operation\Set::intersect
     */
    function intersect($sequence, $expression = null, callable $comparator = null) {
        if (is_array($sequence)) {
            $sequence = new \ArrayIterator($sequence);
        } elseif (!($sequence instanceof \Iterator)) {
            throw new WrongTypeException($sequence, 'array|\Iterator', 'The sequence');
        }
        /** @var callable $expression */
        $expression = Exp::instanceFrom($expression);

        $element = function (\Iterator $iterator) use ($sequence, $expression, $comparator) {
                $result = new IntersectIterator($iterator, $sequence);
                $result->getIndex()->addKey(new SingleKey($expression, $comparator));
                return $result;
        };

        /** @var $this \Qmaker\Linq\Meta\MetaAware */
        $this->meta->getCurrent()->addItem($element);
        return $this;
    }

    /**
     * @see \Qmaker\Linq\Operation\Set::union
     */
    function union($sequence, $expression = null, callable $comparator = null) {
        if (is_array($sequence)) {
            $sequence = new \ArrayIterator($sequence);
        } elseif (!($sequence instanceof \Iterator)) {
            throw new WrongTypeException($sequence, 'array|\Iterator', 'The sequence');
        }
        /** @var callable $expression */
        $expression = Exp::instanceFrom($expression);

        $element = function (\Iterator $iterator) use ($sequence, $expression, $comparator) {
                $iterator2 = new \AppendIterator();
                $iterator2->append($iterator);
                $iterator2->append($sequence);

                $result = new DistinctIterator($iterator2);
                $result->getIndex()->addKey(new SingleKey($expression, $comparator));
                return $result;
        };

        /** @var $this \Qmaker\Linq\Meta\MetaAware */
        $this->meta->getCurrent()->addItem($element);
        return $this;
    }
}