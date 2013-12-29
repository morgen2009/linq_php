<?php

namespace Qmaker\Linq\Iterators;

use Qmaker\Linq\Iterators\Key\Storage;

/**
 * Class IndexIterator
 *
 * Iterate over sorted iterator. Sort order is specified by a set of keys.
 *
 * @package Qmaker\Linq\Iterators
 */
class IndexIterator extends LazyIterator implements \SeekableIterator, RelationInterface
{
    /**
     * @var \Iterator
     */
    private $source;

    /**
     * Description of each key
     * @var Storage
     */
    protected $index = null;

    /**
     * Constructor
     * @param \Iterator $source
     */
    public function __construct(\Iterator $source) {
        $this->source = $source;
        $this->index = new Storage(Storage::WITH_VALUES);
    }

    /**
     * @return Storage
     */
    public function getIndex() {
        return $this->index;
    }

    /**
     * @see \Iterator::current
     */
    public function current() {
        return parent::current()->value;
    }

    /**
     * @see LazyIterator::build()
     */
    protected function build() {
        $this->index->load(iterator_to_array($this->source));
        return new \ArrayIterator($this->index->getData());
    }

    /**
     * Get key values for the current element
     * @return mixed
     */
    public function keys() {
        return parent::current()->key;
    }

    /**
     * @see SeekableIterator::seek
     */
    public function seek($position)
    {
        /** @var \ArrayIterator $iterator */
        $iterator = $this->getInnerIterator();
        $iterator->seek($position);
    }

    /**
     * @see RelationInterface::getRelatedIterators
     */
    public function getRelatedIterators()
    {
        return [ $this->source ];
    }
}