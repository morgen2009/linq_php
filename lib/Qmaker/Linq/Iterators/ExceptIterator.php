<?php

namespace Qmaker\Linq\Iterators;

use Qmaker\Linq\Iterators\Key\Storage;

/**
 * Class ExceptIterator
 *
 * @package Qmaker\Linq\Iterators
 */
class ExceptIterator extends \FilterIterator
{
    /**
     * @var \Iterator
     */
    private $iteratorSub;

    /**
     * @var Storage
     */
    public $index = null;

    /**
     * Constructor
     * @param \Iterator $iterator
     * @param \Iterator $iteratorSub
     */
    public function __construct(\Iterator $iterator, \Iterator $iteratorSub) {
        parent::__construct($iterator);
        $this->iteratorSub = $iteratorSub;
        $this->index = new Storage();
    }

    /**
     * @return Storage
     */
    public function getIndex() {
        return $this->index;
    }

    /**
     * @see \FilterIterator::accept()
     */
    public function accept() {
        $current = $this->current();
        $key = $this->index->getKey()->compute($current);
        return $this->index->push($key) < 0;
    }

    /**
     * @see \Iterator::rewind()
     */
    public function rewind()
    {
        $this->index->load(iterator_to_array($this->iteratorSub));
        parent::rewind();
    }
}