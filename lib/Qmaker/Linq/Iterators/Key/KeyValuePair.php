<?php

namespace Qmaker\Linq\Iterators\Key;

class KeyValuePair {
    public $key;

    public $value;

    public function __construct($key = null, $value = null) {
        $this->key   = $key;
        $this->value = $value;
    }
} 