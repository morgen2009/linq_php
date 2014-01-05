<?php

namespace Qmaker\Iterators;

/**
 * Class ProductIterator
 *
 * Make cross-product of two or more iterators
 */
class ProductIterator implements \Iterator, RelationInterface
{
    /**
     * Position of the current element
     * @var int
     */
    private $position;

    /**
     * @var boolean
     */
    private $hasNext;

    /**
     * @var \Iterator[]
     */
    protected $iterators = [];

    /**
     * Attach iterator
     * @param \Iterator $iterator
     * @param string $info
     */
    public function attachIterator(\Iterator $iterator, $info = null) {
        if (empty($info)) {
            $this->iterators[] = $iterator;
        } else {
            $this->iterators[$info] = $iterator;
        }
    }

    /**
     * @see \Iterator::current
     */
    public function current()
    {
        $result = [];
        foreach ($this->iterators as $name => $iterator) {
            $result[$name] = $iterator->current();
        }
        return $result;
    }

    /**
     * @see \Iterator::next
     */
    public function next()
    {
        $this->position++;

        // shift iterators
        end($this->iterators);
        do {
            /** @var \Iterator $iterator */
            $iterator = current($this->iterators);
            $iterator->next();
            if ($iterator->valid()) {
                return;
            } else {
                $iterator->rewind();
            }
        } while (prev($this->iterators));

        $this->hasNext = false;
    }

    /**
     * @see \Iterator::key
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * Get array of keys of the included iterators
     * @return array
     */
    public function keys()
    {
        $result = [];
        foreach ($this->iterators as $name => $iterator) {
            $result[$name] = $iterator->key();
        }
        return $result;
    }

    /**
     * @see \Iterator::valid
     */
    public function valid()
    {
        return $this->hasNext;
    }

    /**
     * @see \Iterator::rewind
     */
    public function rewind()
    {
        $this->hasNext = true;
        $this->position = 0;
        array_walk($this->iterators, function (\Iterator $iterator) {
            $iterator->rewind();
        });
    }
    /**
     * @see RelationInterface::getRelatedIterators
     */
    public function getRelatedIterators()
    {
        return $this->iterators;
    }
}