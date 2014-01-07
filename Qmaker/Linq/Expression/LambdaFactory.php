<?php

namespace Qmaker\Linq\Expression;


class LambdaFactory {
    static public function create($input) {
        if ($input instanceof LambdaInterface) {
            return $input;
        } elseif (is_string($input)) {
            return self::param($input);
        } elseif (is_array($input)) {
            $input = array_map(function ($item) {
                return LambdaFactory::create($item);
            }, $input);
            return self::set($input);
        } elseif (is_callable($input)) {
            return self::call($input);
        }
        return $input;
    }

    protected function __construct() {
    }

    static public function param($path = null) {
        return null;
    }

    static public function enum($path = null) {
        return null;
    }

    static public function call(callable $callback) {
        return $callback;
    }

    static public function set(array $items) {
        return function ($value, \Iterator $iterator) use ($items) {
            return array_map(function ($item) use ($value, $iterator) {
                    return call_user_func($item, $value, $iterator);
                },
                $items);
        };
    }
}