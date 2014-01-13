<?php

namespace Qmaker\Linq\Expression\Operation;

class Path implements OperationInterface
{
    /**
     * @var string[]
     */
    protected $path = [];

    /**
     * @var callable[]
     */
    protected $action = null;

    /**
     * @param null|string $path
     * @throws \InvalidArgumentException
     */
    public function __construct($path = null) {
        if (!empty($path)) {
            $this->addPath($path);
        }
    }

    public function addPath($path) {
        $matches = explode('.', $path);
        foreach ($matches as $field) {
            if ( !(preg_match('/^([\w]+)$/', $field) > 0) ) {
                throw new \InvalidArgumentException("Expression {$path} does not match the pattern");
            }
        }
        $this->path = array_merge($this->path, $matches);
    }

    /**
     * @see \Qmaker\Linq\Expression\Operation\OperationInterface::compute
     */
    function compute(\SplStack $stack)
    {
        // get current value from stack (only first argument)
        $count = $stack->pop();
        $value = $stack->pop();
        while ($count > 1) {
            $stack->pop();
            $count--;
        }

        if (empty($this->action)) {
            $action = [];
            foreach ($this->path as $item) {
                $callback = $this->buildItemCallback($value, $item);
                if (empty($callback)) {
                    return null;
                }
                $value = call_user_func($callback, $value);
                $action[] = $callback;
            }
            $this->action = $action;
        } else {
            foreach ($this->action as $callback) {
                $value = call_user_func($callback, $value);
            }
        }

        $stack->push($value);
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
}