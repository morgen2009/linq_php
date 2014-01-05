<?php

namespace Qmaker\Iterators;

/**
 * Class JoinIterator
 *
 * Join two iterators
 */
class JoinIterator implements \Iterator, RelationInterface
{
    /**
     * The left iterator to be joined
     * @var \Iterator
     */
    protected $iteratorA;

    /**
     * @var callable
     */
    protected $keyExtractorA;

    /**
     * The right iterator to be joined
     * @var IndexIterator
     */
    protected $iteratorB;

    /**
     * The field name related to the value from the left iterator
     * @var string
     */
    protected $nameA = 'left';

    /**
     * The field name related to the value from the right iterator
     * @var string
     */
    protected $nameB = 'right';

    /**
     * @var LimitIterator
     */
    protected $windowB;

    /**
     * Offset of the current element in the generated sequence
     * @var int
     */
    protected $position;

    /**
     * @var mixed
     */
    protected $keyCurrent;

    /**
     * @param \Iterator $iteratorA
     * @param callable $keyExtractorA
     * @param IndexIterator $iteratorB
     */
    public function __construct(\Iterator $iteratorA, callable $keyExtractorA, IndexIterator $iteratorB) {
        $this->iteratorA = $iteratorA;
        $this->keyExtractorA = $keyExtractorA;
        $this->iteratorB = $iteratorB;

        $this->windowB = new LimitIterator($iteratorB, 0, -1);
    }

    /**
     * @param string $name
     */
    public function setLeftName($name)
    {
        $this->nameA = $name;
    }

    /**
     * @return string
     */
    public function getLeftName()
    {
        return $this->nameA;
    }

    /**
     * @param string $name
     */
    public function setRightName($name)
    {
        $this->nameB = $name;
    }

    /**
     * @return string
     */
    public function getRightName()
    {
        return $this->nameB;
    }

    /**
     * @see \Iterator::current()
     * @return array
     */
    public function current()
    {
        return [
            $this->nameA => $this->iteratorA->current(),
            $this->nameB => $this->windowB->current()
        ];
    }

    /**
     * @see \Iterator::key()
     */
    public function key()
    {
        return $this->keyCurrent;
    }

    /**
     * @return mixed
     */
    public function position() {
        return $this->position;
    }

    /**
     * @see \Iterator::next()
     */
    public function next()
    {
        $this->position++;
        $this->windowB->next();

        if (!$this->windowB->valid()) {
            $keyPrev = $this->keyCurrent;
            $this->iteratorA->next();
            if (!$this->iteratorA->valid()) {
                return;
            }
            $this->iteratorA_extractKey();

            if ($this->iteratorB->compare($this->keyCurrent, $keyPrev) !== 0) {
                while ($this->iteratorA->valid() && !$this->buildWindowB($this->keyCurrent)) {
                    $this->iteratorA->next();
                    $this->iteratorA_extractKey();
                };
             } else {
                $this->windowB->rewind();
            }
        }
    }

    /**
     * @see \Iterator::valid()
     * @return bool
     */
    public function valid()
    {
        return $this->iteratorA->valid();
    }

    /**
     * @see \Iterator::rewind()
     */
    public function rewind()
    {
        $this->position = 0;

        // init the left iterator
        $this->iteratorA->rewind();
        $this->iteratorA_extractKey();

        // find first join
        while ($this->iteratorA->valid() && !$this->buildWindowB($this->keyCurrent)) {
            $this->iteratorA->next();
            $this->iteratorA_extractKey();
        };
    }

    protected function iteratorA_extractKey() {
        if ($this->iteratorA->valid()) {
            $this->keyCurrent = call_user_func($this->keyExtractorA, $this->iteratorA->current(), $this->iteratorA);
        } else {
            $this->keyCurrent = null;
        }
    }

    protected function buildWindowB($key) {
        // find first join
        $offset = $this->iteratorB->search($key);
        if ($offset < 0) {
            return false;
        }

        // count the elements with the same key
        $this->iteratorB->seek($offset);
        $count = 0;
        while ($this->iteratorB->valid() && ($this->iteratorB->compare($this->iteratorB->key(), $this->keyCurrent) == 0)) {
            $this->iteratorB->next();
            $count++;
        }

        $this->windowB->setLimit($offset, $count-1);

        return true;
    }

    /**
     * @see RelationInterface::getRelatedIterators
     */
    public function getRelatedIterators()
    {
        return [ $this->nameA => $this->iteratorA, $this->nameB => $this->iteratorB ];
    }
}