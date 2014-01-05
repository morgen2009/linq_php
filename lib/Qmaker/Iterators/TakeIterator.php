<?php

namespace Qmaker\Iterators;

/**
 * Class TakeIterator
 *
 * Iterate over the sequence until the callback returns false.
 */
class TakeIterator extends \FilterIterator
{
    /**
     * @var callback
     * @param mixed $value
     * @return bool
     */
    protected $predicate;

    /**
     * @var boolean
     */
    protected $accepted;

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
     * @see \Iterator::accept
     */
    public function accept() {
        return ($this->accepted = call_user_func($this->predicate, $this->current(), $this));
    }

    /**
     * @see \Iterator::rewind
     */
    public function rewind() {
        parent::rewind();
        $this->accepted = true;
    }

    /**
     * @see \Iterator::valid
     */
    public function valid()
    {
        return $this->accepted && parent::valid();
    }
}