<?php

namespace Qmaker\Iterators;

/**
 * Class LimitIterator
 */
class LimitIterator implements \OuterIterator
{
    /**
     * @var \Iterator
     */
    protected $iterator;

    /**
     * @var int
     */
    protected $offset;

    /**
     * @var int
     */
    protected $count;

    /**
     * @var int
     */
    protected $position;

    /**
     * Constructor
     * @param \Iterator $iterator
     * @param int $offset
     * @param int $count
     */
    public function __construct(\Iterator $iterator, $offset = null, $count = null) {
        $this->iterator = $iterator;
        $this->setLimit($offset, $count);
    }

    /**
     * @see \Iterator::current
     */
    public function current()
    {
        return $this->iterator->current();
    }

    /**
     * @see \Iterator::next
     */
    public function next()
    {
        $this->position++;
        $this->iterator->next();
    }

    /**
     * @see \Iterator::key
     */
    public function key()
    {
        return $this->iterator->key();
    }

    /**
     * @see \Iterator::valid
     */
    public function valid()
    {
        if (!$this->iterator->valid()) {
            return false;
        }
        if (!empty($this->count) && $this->position >= $this->offset + $this->count) {
            return false;
        };
        return true;
    }

    /**
     * @see \Iterator::rewind
     */
    public function rewind()
    {
        $this->position = $this->offset;

        if ($this->iterator instanceof \SeekableIterator) {
            $this->iterator->seek($this->position);
        } else {
            $this->iterator->rewind();
            $rest = $this->position;
            while ($rest > 0 && $this->iterator->valid()) {
                $this->iterator->next();
                $rest--;
            }
        }
    }

    /**
     * @see \OuterIterator::getInnenIterator
     */
    public function getInnerIterator()
    {
        return $this->iterator;
    }

    /**
     * @param int $offset
     * @param int $count
     */
    public function setLimit($offset = 0, $count = null) {
        $this->offset = empty($offset) ? 0 : $offset;
        $this->count = $count;

        $this->rewind();
    }
}