<?php

namespace Qmaker\Linq\Iterators;

use Qmaker\Linq\Iterators\Key\Storage;

/**
 * Class GroupingIterator
 *
 * Group the element of the inner iterator with the same key value
 *
 * @package Qmaker\Linq\Iterators
 */
class GroupingIterator extends LazyIterator implements RelationInterface
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
     * @see LazyIterator::build
     */
    public function build() {
        foreach ($this->source as $value) {
            $pair = $this->index->createKey($value);

            $position = $this->index->push($pair);
            if ($position < 0) {
                $pair->value = new GroupIterator($this);
            } else {
                $pair->value = $this->index->getData()[$position]->value;
            }

            $pair->value->append($value);
        }
        return new \ArrayIterator($this->index->getData());
    }

    /**
     * @see \Iterator::current
     */
    public function current()
    {
        return parent::current()->value;
    }

    /**
     * @return mixed
     */
    public function keys() {
        return parent::current()->key;
    }

    /**
     * @see RelationInterface::getRelatedIterators
     */
    public function getRelatedIterators()
    {
        return [ $this->source ];
    }
}