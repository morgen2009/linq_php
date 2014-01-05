<?php

namespace Qmaker\Iterators;

/**
 * Class CallbackFilterIterator
 *
 * Filter the sequence with multiple callback functions. Similar to \CallbackFilterIterator, but for multiple callbacks
 */
class CallbackFilterIterator extends \FilterIterator
{
    /**
     * Array of filtering callbacks
     * @var callable[]
     */
    protected $callback;

    /**
     * Constructor
     * @param \Iterator $iterator
     * @param callable $callback
     */
    public function __construct(\Iterator $iterator, callable $callback) {
        parent::__construct($iterator);
        $this->addCallback($callback);
    }

    /**
     * Add filtering callback
     * @param callable $callback
     */
    public function addCallback(callable $callback) {
        $this->callback[] = $callback;
    }

    /**
     * @see \FilterIterator::accept
     */
    public function accept()
    {
        foreach ($this->callback as $item) {
            if (call_user_func($item, $this->current(), $this) === false) {
                return false;
            }
        }
        return true;
    }
}