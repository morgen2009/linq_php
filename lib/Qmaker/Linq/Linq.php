<?php

namespace Qmaker\Linq;

class Linq
{
    /**
     * @var array
     */
    static protected $extensions = [];

    /**
     * @param mixed $expression Expression to compute iterator for LinqExpression
     * @return LinqExpression
     */
    public static function exp($expression = '')
    {
        $object = new LinqExpression($expression);
        return $object;
    }

    /**
     * @param array|\Iterator|callable|string $source
     * @return LinqExecute
     * @see \Qmaker\Linq\Operation\Generation::from
     */
    public static function from($source) {
        $linq = new LinqExecute();
        return $linq->from($source);
    }

    /**
     * @param int $start
     * @param int $count
     * @return LinqExecute
     * @see \Qmaker\Linq\Operation\Generation::range
     */
    public static function range($start, $count) {
        $linq = new LinqExecute();
        return $linq->range($start, $count);
    }

    /**
     * @param mixed $element
     * @param int $count
     * @return LinqExecute
     * @see \Qmaker\Linq\Operation\Generation::repeat
     */
    public static function repeat($element, $count) {
        $linq = new LinqExecute();
        return $linq->repeat($element, $count);
    }

    /**
     * Register extension
     * @param string $name
     * @param string $source
     * @return bool
     */
    public static function register($name, $source) {
        // get callable
        if (is_callable($source)) {
            $reflection = new \ReflectionFunction($source);
            $parameters = $reflection->getParameters();
        } elseif (is_string($source)) {
            $reflection = new \ReflectionClass($source);
            if (!$reflection->hasMethod($name)) {
                return false;
            }
            $source = [$source, $name];
            $parameters = $reflection->getMethod($name)->getParameters();
        } else {
            return false;
        }

        // check parameters of callable
/*        if (count($parameters) != 3) {
            return false;
        }*/

        // add callable into array
        self::$extensions[$name] = $source;
        return true;
    }

    /**
     * Remove extension
     * @param string $name
     * @return bool
     */
    public static function unregister($name) {
        if (isset(self::$extensions[$name])) {
            unset(self::$extensions[$name]);
            return true;
        }
        return false;
    }

    /**
     * Get extension
     * @param $name
     * @return null|mixed
     */
    public static function getExtension($name) {
        if (isset(self::$extensions[$name])) {
            return self::$extensions[$name];
        }
        return null;
    }
}
