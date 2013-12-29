<?php

namespace Qmaker\Linq\Expression;

class PathExpression implements ExpressionInterface
{
    /**
     * @var string[]
     */
    protected $path;

    /**
     * @var callable[]
     */
    protected $callbacks;

    /**
     * @param string $path
     * @throws \InvalidArgumentException
     */
    public function __construct($path) {
        if (empty($path)) {
            throw new \InvalidArgumentException("Path could not be empty for PathExpression");
        }

        $matches = explode('.', $path);

        foreach ($matches as $field) {
            if ( !(preg_match('/^([\w]+)$/', $field) > 0) ) {
                throw new \InvalidArgumentException("Expression {$path} does not match the pattern");
            }
        }

        $this->path = $matches;
        $this->callbacks = null;
    }

    /**
     * @see \Qmaker\Linq\Expression\ExpressionInterface::__invoke()
     */
    function __invoke($value, \Iterator $iterator = null)
    {
        if (empty($this->callbacks)) {
            $callbacks = [];
            foreach ($this->path as $item) {
                $callback = $this->buildItemCallback($value, $item);
                if (empty($callback)) {
                    return null;
                }
                $callbacks[] = $callback;

                $value = call_user_func($callback, $value);
            }
            $this->callbacks = $callbacks;
            return $value;
        } else {
            foreach ($this->callbacks as $callback) {
                $value = call_user_func($callback, $value);
            }
            return $value;
        }
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

            // look for public getter
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
            if (isset($value[$item])) {
                return function ($value) use ($item) {
                    return empty($value) ? null : $value[$item];
                };
            }
        }
        return null;
    }
}