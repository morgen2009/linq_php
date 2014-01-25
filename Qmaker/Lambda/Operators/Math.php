<?php

namespace Qmaker\Lambda\Operators;


use Qmaker\Lambda\OperatorInterface;

class Math implements OperatorInterface {

    const ADD   = 1;
    const SUB   = 2;
    const MULT  = 3;
    const DIV   = 4;
    const POWER = 5;

    protected $operator;

    public function __construct($operator) {
        $this->operator = $operator;
    }

    protected static $instances = [];

    /**
     * Create single instance for each operator
     * @param $operator
     * @return mixed
     */
    public static function instance($operator) {
        if (!isset(self::$instances[$operator])) {
            self::$instances[$operator] = new self($operator);
        }
        return self::$instances[$operator];
    }

    /**
     * @see OperatorInterface::apply
     */
    public function apply(array &$stack)
    {
        $count = array_pop($stack);
        $result = array_pop($stack);
        while ($count > 1) {
            switch ($this->operator) {
                case self::ADD:   $result += array_pop($stack); break;
                case self::SUB:   $result -= array_pop($stack); break;
                case self::MULT:  $result *= array_pop($stack); break;
                case self::DIV:   $result /= array_pop($stack); break;
                case self::POWER: $result = pow($result, array_pop($stack)); break;
            }
            $count--;
        };
        array_push($stack, $result);
    }

    /**
     * @see OperatorInterface::getPriority
     */
    public function getPriority()
    {
        switch ($this->operator) {
            case self::ADD:   return 6;
            case self::SUB:   return 6;
            case self::MULT:  return 7;
            case self::DIV:   return 7;
            case self::POWER: return 8;
            default: return PHP_INT_MAX;
        }
    }

    /**
     * @see OperatorInterface::getMaxCount
     */
    public function getMaxCount()
    {
        return PHP_INT_MAX;
    }
}