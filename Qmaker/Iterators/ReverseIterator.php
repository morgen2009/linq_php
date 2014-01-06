<?php

namespace Qmaker\Iterators;

/**
 * Class ReverseIterator
 *
 * Reverse the order in the sequence
 */
class ReverseIterator extends LazyIterator implements RelationInterface
{
    /**
     * @var \Iterator
     */
    protected $iterator = null;

    /**
     * Constructor
     * @param \Iterator $iterator
     */
    public function __construct(\Iterator $iterator) {
        $this->iterator = $iterator;
    }

    /**
     * @see LazyIterator::build
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