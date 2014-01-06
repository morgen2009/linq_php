<?php

namespace Qmaker\Iterators;

/**
 * Class IntersectIterator
 */
class IntersectIterator extends ExceptIterator
{
    /**
     * @see \FilterIterator::accept
     */
    public function accept()
    {
        $this->key = call_user_func($this->keyExtractor, $this->current(), $this);

        if ($this->set->offsetExists($this->key)) {
            $this->set->offsetUnset($this->key);
            return true;
        } else {
            return false;
        }
    }
}