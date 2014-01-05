<?php

namespace Qmaker\Iterators\Collections;

class Lookup extends Dictionary {

    public function __construct()
    {
    }

    public function append($key, $value) {
        if ($this->offsetExists($key)) {
            $storage = $this->offsetGet($key);
        } else {
            $storage = new \ArrayIterator();
            $this->offsetSet($key, $storage);
        }
        $storage->append($value);
    }
}