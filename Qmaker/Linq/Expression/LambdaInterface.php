<?php

namespace Qmaker\Linq\Expression;


interface LambdaInterface {
    /**
     * @param mixed $value
     * @param \Iterator $iterator
     * @return mixed
     */
    function __invoke($value, \Iterator $iterator = null);
} 