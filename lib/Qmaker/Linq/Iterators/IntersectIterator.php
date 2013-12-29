<?php

namespace Qmaker\Linq\Iterators;

use Qmaker\Linq\Iterators\Key\Storage;

/**
 * Class IntersectIterator
 *
 * @package Qmaker\Linq\Iterators
 */
class IntersectIterator extends \FilterIterator
{
    /**
     * @var \Iterator
     */
    private $iteratorB;

    /**
     * @var Storage
     */
    public $index = null;

    /**
     * Constructor
     * @param \Iterator $iterator
     * @param \Iterator $iteratorB
     */
    public function __construct(\Iterator $iterator, \Iterator $iteratorB) {
        parent::__construct($iterator);
        $this->iteratorB = $iteratorB;
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
        return $this->index->pop($key) !== null;
    }

    /**
     * @see \Iterator::rewind()
     */
    public function rewind()
    {
        $this->index->load(iterator_to_array($this->iteratorB));
        parent::rewind();
    }
}