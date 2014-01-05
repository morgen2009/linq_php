<?php

namespace Qmaker\Iterators;

use Qmaker\Iterators\Collections\OrderedDictionary;

/**
 * Class IndexIterator
 *
 * Get sorted sequence
 */
class IndexIterator extends LazyIterator implements \SeekableIterator, RelationInterface
{
    /**
     * @var \Iterator
     */
    private $source;

    /**
     * Compute key by value
     * @var callable
     */
    protected $keyExtractor = null;

    /**
     * @var OrderedDictionary
     */
    protected $items;

    /**
     * Constructor
     * @param \Iterator $source
     * @param callable $keyExtractor
     * @param callable $comparator
     */
    public function __construct(\Iterator $source, callable $keyExtractor, callable $comparator) {
        $this->source = $source;
        $this->keyExtractor = $keyExtractor;
        $this->items = new OrderedDictionary($comparator);
    }

    /**
     * @see LazyIterator::build
     */
    protected function build() {
        $this->items->load($this->source);
        return $this->items;
    }

    /**
     * @see SeekableIterator::seek
     */
    public function seek($position)
    {
        $this->items->seek($position);
    }

    /**
     * @see OrderedDictionary::search
     */
    public function search($key)
    {
        return $this->items->search($key);
    }

    /**
     * @see OrderedDictionary::position
     */
    public function position()
    {
        return $this->items->position();
    }

    /**
     * @see RelationInterface::getRelatedIterators
     */
    public function getRelatedIterators()
    {
        return [ $this->source ];
    }

    /**
     * @see ComparerInterface::compare
     */
    public function compare($x, $y) {
        return $this->items->compare($x, $y);
    }
}