<?php

namespace Qmaker\Iterators;

/**
 * Class OuterJoinIterator
 *
 * Join two iterators
 */
class OuterJoinIterator extends JoinIterator
{
    protected $validWindowB = null;

    /**
     * @see \Iterator::current()
     * @return array
     */
    public function current()
    {
        return [
            $this->nameA => $this->iteratorA->current(),
            $this->nameB => empty($this->validWindowB) ? null : $this->windowB->current()
        ];
    }

    /**
     * @see \Iterator::next()
     */
    public function next()
    {
        $this->position++;
        $this->windowB->next();

        if (!$this->validWindowB || !$this->windowB->valid()) {
            $keyPrev = $this->keyCurrent;
            $this->iteratorA->next();
            if (!$this->iteratorA->valid()) {
                return;
            }
            $this->iteratorA_extractKey();

            if ($this->iteratorB->compare($this->keyCurrent, $keyPrev) !== 0) {
                $this->validWindowB = $this->buildWindowB($this->keyCurrent);
             } else {
                $this->windowB->rewind();
            }
        }
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
        $this->iteratorB->rewind();
        $this->validWindowB = $this->buildWindowB($this->keyCurrent);
    }
}