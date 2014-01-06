<?php

namespace Qmaker\Linq\Expression;

class Exp {
    /**
     * Create Expression object from mixed value
     *
     * @param mixed $expression
     * @return ExpressionInterface
     * @throws \InvalidArgumentException
     */
    public static function instanceFrom($expression) {
        if (empty($expression)) {
            return new DummyExpression();
        } elseif (is_string($expression)) {
            // $expression like 'id'
            return new PathExpression($expression);

        } elseif ($expression instanceof ExpressionInterface) {
            // instance of ExpressionInterface
            return $expression;

        } elseif (is_callable($expression)) {
            // $expression like function ($x) { ... } or [$object, 'method']
            return new CallbackExpression($expression);

        } elseif (is_array($expression)) {
            // $expression like ['rec1' => $expression1, 'rec2' => $expression2 ]
            $code = [];
            foreach ($expression as $name => $item) {
                $code[$name] = self::instanceFrom($item);
            }
            return new ArrayExpression($code);
        } else {
            throw new \InvalidArgumentException('Expression must be callable|string|array');
        }
    }

    public static function group($name = '') {
        $path = empty($name) ? 'keys' : 'keys.' . (string)$name;
        return new IteratorPathExpression($path, '\Qmaker\Linq\Iterators\GroupingIterator');
    }

    public static function func(callable $callback) {
        return new CallbackExpression($callback);
    }

    public static function path($name) {
        return new PathExpression((string)$name);
    }

    public static function index($name = '') {
        $path = empty($name) ? 'keys' : 'keys.' . (string)$name;
        return new IteratorPathExpression($path, '\Qmaker\Linq\Iterators\IndexIterator');
    }

    public static function isEqual($expression, $value) {
        return new ComparisonExpression(self::instanceFrom($expression), $value, ComparisonExpression::EQUAL);
    }

    public static function isGreater($expression, $value) {
        return new ComparisonExpression(self::instanceFrom($expression), $value, ComparisonExpression::GREATER);
    }

    public static function isLess($expression, $value) {
        return new ComparisonExpression(self::instanceFrom($expression), $value, ComparisonExpression::LESS);
    }
}