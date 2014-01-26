<?php

namespace Qmaker\Lambda\Operators;


use Qmaker\Lambda\OperatorInterface;

class Logical implements OperatorInterface {

    const _AND_  = 1;
    const _OR_   = 2;
    const _XOR_  = 3;
    const _NOT_  = 4;

    protected $operator;

    protected static $instances = [];

    public function __construct($operator) {
        $this->operator = $operator;
    }

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
        switch ($this->operator) {
            case self::_AND_: {
                $result = true;
                while ($count > 0) {
                    $result = array_pop($stack) && $result;
                    $count--;
                };
                array_push($stack, $result);
                break;
            }
            case self::_OR_: {
                $result = false;
                while ($count > 0) {
                    $result = array_pop($stack) || $result;
                    $count--;
                };
                array_push($stack, $result);
                break;
            }
            case self::_XOR_: {
                $result = false;
                while ($count > 0) {
                    $result = (array_pop($stack) xor $result);
                    $count--;
                };
                array_push($stack, $result);
                break;
            }
            case self::_NOT_: {
                $result = array_pop($stack);
                while ($count > 1) {
                    array_pop($stack);
                    $count--;
                };
                array_push($stack, !$result);
                break;
            }
        }
    }

    /**
     * @see OperatorInterface::getPriority
     */
    public function getPriority()
    {
        switch ($this->operator) {
            case self::_AND_: return 3;
            case self::_OR_:  return 1;
            case self::_XOR_: return 2;
            case self::_NOT_: return 3.5;
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