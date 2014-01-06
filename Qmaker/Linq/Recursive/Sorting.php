<?php

namespace Qmaker\Linq\Recursive;

use Qmaker\Linq\Expression\Exp;
use Qmaker\Linq\Iterators\IndexIterator;
use Qmaker\Linq\Iterators\Key\SingleKey;
use Qmaker\Linq\Iterators\ReverseIterator;

trait Sorting
{
    private function _orderBy($expression, $reverse, callable $comparator = null) {
        /** @var \Qmaker\Linq\Meta\MetaAware $this */
        /** @var callable $expression */
        $expression = Exp::instanceFrom($expression);

        $element = function (\Iterator $iterator) use ($expression, $reverse, $comparator) {
            if (!($iterator instanceof IndexIterator)) {
                $iterator = new IndexIterator($iterator);
            }
            $iterator->getIndex()->addKey(new SingleKey($expression, $comparator, $reverse));
            return $iterator;
        };

        /** @var $this \Qmaker\Linq\Meta\MetaAware */
        $this->meta->getCurrent()->addItem($element);
        return $this;
    }

    /**
     * @see \Qmaker\Linq\Operation\Sorting::orderBy
     */
    function orderBy($expression, callable $comparator = null) {
        return $this->_orderBy($expression, false, $comparator);
    }

    /**
     * @see \Qmaker\Linq\Operation\Sorting::orderByDescending
     */
    function orderByDescending($expression, callable $comparator = null) {
        return $this->_orderBy($expression, true, $comparator);
    }

    /**
     * @see \Qmaker\Linq\Operation\Sorting::thenBy
     */
    function thenBy($expression, callable $comparator = null) {
        return $this->_orderBy($expression, false, $comparator);
    }

    /**
     * @see \Qmaker\Linq\Operation\Sorting::thenByDescending
     */
    function thenByDescending($expression, callable $comparator = null) {
        return $this->_orderBy($expression, true, $comparator);
    }

    /**
     * @see \Qmaker\Linq\Operation\Sorting::reverse
     */
    function reverse() {
        $element = function (\Iterator $iterator) {
            if ($iterator instanceof IndexIterator) {
                $iterator->getIndex()->getKey()->setReverse(true);
                return $iterator;
            } else {
                return new ReverseIterator($iterator);
            }
        };

        /** @var $this \Qmaker\Linq\Meta\MetaAware */
        $this->meta->getCurrent()->addItem($element);
        return $this;
    }

    /**
     * @see \Qmaker\Linq\Operation\Sorting::order
     */
    function order(callable $comparator = null) {
        $element = function (\Iterator $iterator) use ($comparator) {
            if (!($iterator instanceof IndexIterator)) {
                $iterator = new IndexIterator($iterator);
            }
            $iterator->getIndex()->addKey(new SingleKey(function ($item) { return $item; }, $comparator, false));
            return $iterator;
        };

        /** @var $this \Qmaker\Linq\Meta\MetaAware */
        $this->meta->getCurrent()->addItem($element);
        return $this;
    }
}