<?php

namespace Qmaker\Linq\Iterators;

/**
 * Class ProductIterator
 *
 * Make cross-product of two or more iterators
 *
 * @package Qmaker\Linq\Iterators
 */
class ProductIterator implements \Iterator, RelationInterface
{
    /**
     * Offset of the current element in the generated sequence
     * @var int
     */
    private $offset;

    /**
     * @var boolean
     */
    private $eos;

    /**
     * @var \Iterator[]
     */
    protected $iterators = [];

    /**
     * Attach iterator
     * @param \Iterator $iterator
     * @param string $name
     */
    public function attachIterator(\Iterator $iterator, $name = null) {
        if (empty($name)) {
            $this->iterators[] = $iterator;
        } else {
            $this->iterators[$name] = $iterator;
        }
    }

    /**
     * @see \OuterIterator::current()
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
     * @see \OuterIterator::next()
     */
    public function next()
    {
        $this->offset++;

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

        $this->eos = true;
    }

    /**
     * @see \OuterIterator::key()
     */
    public function key()
    {
        return $this->offset;
    }

    /**
     * @see \OuterIterator::valid()
     */
    public function valid()
    {
        return !$this->eos;
    }

    /**
     * @see \OuterIterator::rewind()
     */
    public function rewind()
    {
        $this->eos = false;
        $this->offset = 0;
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