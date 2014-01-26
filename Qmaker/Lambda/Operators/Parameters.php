<?php

namespace Qmaker\Lambda\Operators;


class Parameters {

    protected $offset;

    public function __construct($offset)
    {
        $this->offset = $offset;
    }

    public function __invoke()
    {
        $params = func_get_args();
        return isset($params[$this->offset]) ? $params[$this->offset] : null;
    }
}