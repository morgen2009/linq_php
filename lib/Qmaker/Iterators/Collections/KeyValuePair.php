<?php

namespace Qmaker\Iterators\Collections;

class KeyValuePair {

    /**
     * @var mixed
     */
    public $key;

    /**
     * @var mixed
     */
    public $value;

    /**
     * Constructor
     * @param mixed $key
     * @param mixed $value
     */
    public function __construct($key = null, $value = null) {
        $this->key   = $key;
        $this->value = $value;
    }
} 