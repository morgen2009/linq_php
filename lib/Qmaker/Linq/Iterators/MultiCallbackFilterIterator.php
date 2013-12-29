<?php

namespace Qmaker\Linq\Iterators;

/**
 * Class MultiCallbackFilterIterator
 *
 * Filter the inner iterator using multiple callback functions. Similar with \CallbackFilterIterator, but for multiple callbacks
 *
 * @package Qmaker\Linq\Iterators
 */
class MultiCallbackFilterIterator extends \FilterIterator
{
    /**
     * Array of filtering callback functions
     * @var callable[]
     */
    protected $callback;

    /**
     * Callback with multiple parameters
     * @var bool[]
     */
    protected $multipleParam;

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
     * Add filtering callback function
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