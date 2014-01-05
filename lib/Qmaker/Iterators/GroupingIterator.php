<?php

namespace Qmaker\Iterators;

/**
 * Class GroupingIterator
 *
 * Group the element of the sequence
 */
class GroupingIterator extends LazyIterator implements RelationInterface
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
     * @var Collections\Lookup
     */
    protected $lookup;

    /**
     * Constructor
     * @param \Iterator $source
     * @param callable $keyExtractor
     */
    public function __construct(\Iterator $source, callable $keyExtractor) {
        $this->source = $source;
        $this->keyExtractor = $keyExtractor;
        $this->lookup = new Collections\Dictionary();
    }

    /**
     * @see LazyIterator::build
     */
    public function build() {
        $this->source->rewind();

        while ($this->source->valid()) {
            $current = $this->source->current();
            $key = call_user_func($this->keyExtractor, $current);

            $this->lookup->append($key, $current);
            $this->source->next();
        }

        return $this->lookup;
    }

    /**
     * @see RelationInterface::getRelatedIterators
     */
    public function getRelatedIterators()
    {
        return [ $this->source ];
    }
}