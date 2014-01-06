<?php

namespace Qmaker\Linq\Recursive;

use Qmaker\Linq\Iterators\TakeIterator;
use Qmaker\Linq\Iterators\SkipIterator;

/**
 * @see \Qmaker\Linq\Operation\Partitioning
 */
trait Partitioning
{
    /**
     * @see \Qmaker\Linq\Operation\Partitioning::skip
     */
    function skip($count) {
        $element = function (\Iterator $iterator) use ($count) {
            return new \LimitIterator($iterator, $count);
        };

        /** @var $this \Qmaker\Linq\Meta\MetaAware */
        $this->meta->getCurrent()->addItem($element);
        return $this;
    }

    /**
     * @see \Qmaker\Linq\Operation\Partitioning::skipWhile
     */
    function skipWhile(callable $callback) {
        $element = function (\Iterator $iterator) use ($callback) {
            return new SkipIterator($iterator, $callback);
        };

        /** @var $this \Qmaker\Linq\Meta\MetaAware */
        $this->meta->getCurrent()->addItem($element);
        return $this;
    }

    /**
     * @see \Qmaker\Linq\Operation\Partitioning::take
     */
    function take($count) {
        $element = function (\Iterator $iterator) use ($count) {
            return new \LimitIterator($iterator, 0, $count);
        };

        /** @var $this \Qmaker\Linq\Meta\MetaAware */
        $this->meta->getCurrent()->addItem($element);
        return $this;
    }

    /**
     * @see \Qmaker\Linq\Operation\Partitioning::takeWhile
     */
    function takeWhile(callable $callback) {
        $element = function (\Iterator $iterator) use ($callback) {
            return new TakeIterator($iterator, $callback);
        };

        /** @var $this \Qmaker\Linq\Meta\MetaAware */
        $this->meta->getCurrent()->addItem($element);
        return $this;
    }
}