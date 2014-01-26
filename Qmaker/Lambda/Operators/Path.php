<?php

namespace Qmaker\Lambda\Operators;

use Qmaker\Lambda\OperatorInterface;
use Qmaker\Lambda\ParameterAwareInterface;

class Path implements OperatorInterface, ParameterAwareInterface
{
    /**
     * @var string[]
     */
    protected $path = [];

    /**
     * @var callable[]
     */
    protected $operator = null;

    /**
     * @param null|string $path
     * @throws \InvalidArgumentException
     */
    public function __construct($path = null) {
        if (!empty($path)) {
            $this->addParameter($path);
        }
    }

    /**
     * @see OperatorInterface::getMaxCount
     */
    public function addParameter($parameter) {
        if (!is_string($parameter)) {
            throw new \InvalidArgumentException("Path is not string");
        }
        $matches = explode('.', $parameter);
        foreach ($matches as $field) {
            if ( !(preg_match('/^([\w]+)$/', $field) > 0) ) {
                throw new \InvalidArgumentException("Path {$parameter} does not match the pattern");
            }
        }
        $this->path = array_merge($this->path, $matches);
    }

    /**
     * @see OperatorInterface::apply
     */
    function apply(array &$stack)
    {
        // get current value from stack (only first argument)
        $count = array_pop($stack);
        $value = array_pop($stack);
        while ($count > 1) {
            array_pop($stack);
            $count--;
        }

        if (empty($this->operator)) {
            $operator = [];
            foreach ($this->path as $item) {
                $callback = $this->buildItemCallback($value, $item);
                if (empty($callback)) {
                    return null;
                }
                $value = call_user_func($callback, $value);
                $operator[] = $callback;
            }
            $this->operator = $operator;
        } else {
            foreach ($this->operator as $callback) {
                $value = call_user_func($callback, $value);
            }
        }

        array_push($stack, $value);
    }

    /**
     * Create callback to compute item value
     * @param mixed $value
     * @param mixed $item
     * @return callable
     */
    protected function buildItemCallback($value, $item) {
        if (is_object($value)) {
            $class = get_class($value);

            // look for public properties
            $look = get_class_vars($class);
            if (array_search($item, $look) !== FALSE) {
                return function ($value) use ($item) {
                    return empty($value) ? null : $value->{$item};
                };
            }

            // look for public getter or method
            $look = get_class_methods($class);
            $methods = [ 'get' . ucwords($item), 'is' . ucwords($item), $item];
            foreach ($methods as $method) {
                if (array_search($method, $look) !== FALSE) {
                    return function ($value) use ($method) {
                        return empty($value) ? null : $value->{$method}();
                    };
                }
            }
        } elseif (is_array($value)) {
            // look for key
            if (isset($value[$item]) || array_key_exists($item, $value) !== false) {
                return function ($value) use ($item) {
                    return empty($value) ? null : $value[$item];
                };
            }
        }
        return null;
    }

    /**
     * @see OperatorInterface::getPriority
     */
    public function getPriority()
    {
        return 10;
    }

    /**
     * @see OperatorInterface::getMaxCount
     */
    public function getMaxCount()
    {
        return PHP_INT_MAX;
    }
}