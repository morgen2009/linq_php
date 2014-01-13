<?php

namespace Qmaker\Linq\Expression;


class Lambda {
    protected function __construct()
    {
    }

    /**
     * @param null|string $path
     * @return LambdaInstance
     */
    static public function v($path = null)
    {
        $lambda = (new LambdaInstance())->v();
        if (!empty($path)) {
            $lambda->item($path);
        }
        return $lambda;
    }

    static public function i()
    {
        return (new LambdaInstance())->i();
    }

    static public function c($value)
    {
        return (new LambdaInstance())->c($value);
    }

    static public function complex()
    {
        throw new \BadMethodCallException('Not implemented');
    }

    static public function call(callable $callback)
    {
        throw new \BadMethodCallException('Not implemented');
    }

    static public function math($expression)
    {
        throw new \BadMethodCallException('Not implemented');
    }

    static public function __callStatic($name, $arguments)
    {
        throw new \BadMethodCallException('Not implemented');
    }
}