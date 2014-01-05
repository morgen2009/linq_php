<?php

namespace Qmaker\Iterators;

/**
 * Class LimitIterator
 */
class LimitIterator extends \IteratorIterator
{
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
        parent::__construct($iterator);

        $this->setLimit($offset, $count);
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

    /**
     * @see \Iterator::valid
     */
    public function valid()
    {
        return parent::valid() && empty($this->count) ? true : $this->position < $this->offset + $this->count;
    }

    /**
     * @see \Iterator::rewind
     */
    public function rewind()
    {
        $this->position = $this->offset;
        $iterator = $this->getInnerIterator();

        if ($iterator instanceof \SeekableIterator) {
            $iterator->seek($this->position);
        } else {
            parent::rewind();
            $rest = $this->position;
            while ($rest > 0 && parent::valid()) {
                parent::next();
                $rest--;
            }
        }
    }
}