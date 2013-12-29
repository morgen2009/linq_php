<?php

namespace Qmaker\Linq\Iterators;

/**
 * Class SkipIterator
 *
 * Skip the elements from the inner iterator until the callback return false;
 *
 * @package Qmaker\Linq\Iterators
 */
class SkipIterator extends \FilterIterator
{
    /**
     * @var callback
     * @param mixed $value
     * @return bool
     */
    protected $expression;

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
        return true;
    }

    /**
     * @see \Iterator::rewind()
     */
    public function rewind() {
        parent::rewind();
        while (call_user_func($this->expression, $this->current(), $this)) {
            $this->next();
        }
    }
}