<?php

namespace Qmaker\Fixtures;

use Qmaker\Linq\Expression\ConverterTypeInterface;

class CallbackConverter implements ConverterTypeInterface {

    /**
     * @var callable
     */
    protected $callback;

    /**
     * @param callable $callback
     */
    public function __construct(callable $callback) {
        $this->callback = $callback;
    }

    /**
     * Convert data to other type
     * @param $data
     * @return mixed
     */
    public function convert($data)
    {
        return call_user_func($this->callback, $data);
    }
}
