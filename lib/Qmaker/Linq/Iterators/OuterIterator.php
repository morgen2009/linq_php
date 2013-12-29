<?php

namespace Qmaker\Linq\Iterators;

/**
 * Class OuterIterator
 *
 * Iterate over the inner iterator, which can be specified dynamically after the iterator creation
 *
 * @package Qmaker\Linq\Iterators
 */
class OuterIterator extends LazyIterator
{
    /**
     * @var \Iterator
     */
    protected $iterator = null;

    /**
     * Set internal iterator
     * @param \Iterator $iterator
     */
    public function setInnerIterator(\Iterator $iterator) {
        $this->iterator = $iterator;
        $this->clearInnerIterator();
    }

    /**
     * @see LazyIterator::build()
     */
    protected function build()
    {
        return $this->iterator;
    }
}