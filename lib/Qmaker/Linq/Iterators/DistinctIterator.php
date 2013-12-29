<?php

namespace Qmaker\Linq\Iterators;

use Qmaker\Linq\Iterators\Key\Storage;

/**
 * Class DistinctIterator
 *
 * Gives the elements from the inner iterator with unique keys. The keys are computed by the callbacks
 *
 * @package Qmaker\Linq\Iterators
 */
class DistinctIterator extends \FilterIterator
{
    /**
     * @var Storage
     */
    public $index = null;

    /**
     * Constructor
     * @param \Iterator $iterator
     */
    public function __construct(\Iterator $iterator) {
        parent::__construct($iterator);
        $this->index = new Storage();
    }

    /**
     * @return Storage
     */
    public function getIndex() {
        return $this->index;
    }

    /**
     * @see \FilterIterator::accept
     */
    public function accept() {
        $current = $this->current();
        $key = $this->index->getKey()->compute($current);
        return $this->index->push($key) < 0;
    }
}