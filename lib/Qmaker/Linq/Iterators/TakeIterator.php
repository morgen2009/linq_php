<?php

namespace Qmaker\Linq\Iterators;

/**
 * Class TakeIterator
 *
 * Iterate over the inner iterator until the callback returns false.
 *
 * @package Qmaker\Linq\Iterators
 */
class TakeIterator extends \FilterIterator
{
    /**
     * @var callback
     * @param mixed $value
     * @return bool
     */
    protected $expression;

    /**
     * @var boolean
     */
    protected $live;

    /**
     * Constructor
     * @param \Iterator $iterator
     * @param callable $expression
     */
    public function __construct(\Iterator $iterator, callable $expression) {
        parent::__construct($iterator);
        $this->expression = $expression;
    }

    /**
     * @see \FilterIterator::accept()
     */
    public function accept() {
        return ($this->live = call_user_func($this->expression, $this->current(), $this));
    }

    /**
     * @see \Iterator::rewind()
     */
    public function rewind() {
        parent::rewind();
        $this->live = true;
    }

    /**
     * @see \Iterator::valid()
     */
    public function valid()
    {
        return $this->live && parent::valid();
    }
}