<?php

namespace Qmaker\Linq\Expression;

class ComparisonExpression implements ExpressionInterface
{
    /**
     * @var ExpressionInterface
     */
    protected $expression;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var int|callable
     */
    protected $operation;

    const EQUAL = 1;
    const GREATER = 2;
    const LESS = 3;
    const EQUAL_OR_GREATER = 4;
    const EQUAL_OR_LESS = 5;
    const NOT_EQUAL = 6;

    /**
     * @param ExpressionInterface $expression
     * @param mixed $value
     * @param int|callable $operation
     */
    public function __construct(ExpressionInterface $expression, $value, $operation) {
        $this->expression = $expression;
        $this->value = $value;
        $this->operation = $operation;
    }

    /**
     * @param ExpressionInterface $expression
     */
    public function setExpression(ExpressionInterface $expression)
    {
        $this->expression = $expression;
    }

    /**
     * @return ExpressionInterface
     */
    public function getExpression()
    {
        return $this->expression;
    }

    /**
     * @param callable|int $operation
     */
    public function setOperation($operation)
    {
        $this->operation = $operation;
    }

    /**
     * @return callable|int
     */
    public function getOperation()
    {
        return $this->operation;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @see \Qmaker\Linq\Expression\ExpressionInterface::__invoke()
     */
    function __invoke($value, \Iterator $iterator = null)
    {
        $actualValue = call_user_func($this->expression, $value, $iterator);

        if (is_callable($this->operation)) {
            return call_user_func($this->operation, $actualValue, $this->value);
        } else {
            switch ($this->operation) {
                case self::NOT_EQUAL : {
                    return $actualValue != $this->value;
                    break;
                }
                case self::EQUAL : {
                    return $actualValue == $this->value;
                    break;
                }
                case self::GREATER : {
                    return $actualValue > $this->value;
                    break;
                }
                case self::EQUAL_OR_GREATER : {
                    return $actualValue >= $this->value;
                    break;
                }
                case self::LESS : {
                    return $actualValue < $this->value;
                    break;
                }
                case self::EQUAL_OR_LESS : {
                    return $actualValue <= $this->value;
                    break;
                }
                default : {
                    return false;
                }
            }
        }
    }
}