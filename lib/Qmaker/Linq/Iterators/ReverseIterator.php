<?php

namespace Qmaker\Linq\Iterators;

/**
 * Class ReverseIterator
 *
 * Reverse the order within the inner iterator
 *
 * @package Qmaker\Linq\Iterators
 */
class ReverseIterator extends LazyIterator implements RelationInterface
{
    /** @var \Iterator */
    protected $iterator = null;

    /**
     * Set internal iterator
     * @param \Iterator $iterator
     */
    public function __construct(\Iterator $iterator) {
        $this->iterator = $iterator;
    }

    /**
     * @see LazyIterator::build()
     */
    protected function build() {
        $data = array_reverse(iterator_to_array($this->iterator));
        return new \ArrayIterator($data);
    }

    /**
     * @see RelationInterface::getRelatedIterators
     */
    public function getRelatedIterators()
    {
        return [ $this->iterator ];
    }
}