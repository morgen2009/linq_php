<?php

namespace Qmaker\Iterators;
use Qmaker\Iterators\Collections\HashSet;

/**
 * Class DistinctIterator
 *
 * The sequence of unique elements from the sequence
 *
 * @todo Use OrderedSet instead of HashSet
 */
class DistinctIterator extends \FilterIterator
{
    /**
     * @var HashSet
     */
    protected $set;

    /**
     * Compute key by value
     * @var callable
     */
    protected $keyExtractor;

    /**
     * Current key
     * @var mixed
     */
    protected $key;

    /**
     * Constructor
     * @param \Iterator $iterator
     * @param callable $keyExtractor
     */
    public function __construct(\Iterator $iterator, callable $keyExtractor) {
        parent::__construct($iterator);
        $this->keyExtractor = $keyExtractor;
        $this->set = new HashSet();
    }

    /**
     * @see \Iterator::key
     */
    public function key() {
        return $this->key;
    }

    /**
     * @see \FilterIterator::accept
     */
    public function accept() {
        $this->key = call_user_func($this->keyExtractor, $this->current(), $this);

        if ($this->set->offsetExists($this->key)) {
            return false;
        } else {
            $this->set->offsetSet($this->key);
            return true;
        }
    }
}