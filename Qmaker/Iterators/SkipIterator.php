<?php

namespace Qmaker\Iterators;

/**
 * Class SkipIterator
 *
 * Skip the elements from the sequence until the callback return false;
 */
class SkipIterator extends \FilterIterator
{
    /**
     * @var callback
     * @param mixed $value
     * @return bool
     */
    protected $predicate;

    /**
     * Constructor
     * @param \Iterator $iterator
     * @param callable $predicate
     */
    public function __construct(\Iterator $iterator, callable $predicate) {
        parent::__construct($iterator);
        $this->predicate = $predicate;
    }

    /**
     * @see \FilterIterator::accept
     */
    public function accept() {
        return true;
    }

    /**
     * @see \Iterator::rewind
     */
    public function rewind() {
        parent::rewind();
        while (call_user_func($this->predicate, $this->current(), $this)) {
            $this->next();
        }
    }
}