<?php

namespace Qmaker\Linq\Iterators;

use Iterator;

/**
 * Class LazyIterator
 *
 * Iterate over iterator, which will be built when the first element is fetched
 *
 * @package Qmaker\Linq\Iterators
 */
abstract class LazyIterator implements \OuterIterator
{
    /**
     * @var \Iterator
     */
    private $iterator = null;

    /**
     * @see \Iterator::current()
     */
    public function current()
    {
        return $this->getInnerIterator()->current();
    }

    /**
     * @see \Iterator::next()
     */
    public function next()
    {
        $this->getInnerIterator()->next();
    }

    /**
     * @see \Iterator::key()
     */
    public function key()
    {
        return $this->getInnerIterator()->key();
    }

    /**
     * @see \Iterator::valid()
     */
    public function valid()
    {
        return $this->getInnerIterator()->valid();
    }

    /**
     * @see \Iterator::rewind()
     */
    public function rewind()
    {
        $this->getInnerIterator()->rewind();
    }

    /**
     * Build iterator
     * @return \Iterator
     */
    abstract protected function build();

    /**
     * @see \OuterIterator::getInnenIterator()
     */
    public function getInnerIterator()
    {
        isset($this->iterator) || $this->iterator = $this->build();
        return $this->iterator;
    }

    /**
     * Clear inner iterator
     * @return void
     */
    public function clearInnerIterator() {
        $this->iterator = null;
    }
}