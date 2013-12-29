<?php

namespace Qmaker\Linq\Expression;

class ArrayExpression implements ExpressionInterface
{
    /**
     * @var ExpressionInterface[]
     */
    protected $expressions;

    /**
     * @param ExpressionInterface[] $expression
     * @throws \InvalidArgumentException
     */
    public function __construct(array $expression) {
        foreach ($expression as $name => $exp) {
            // compute field name from string expression (if empty)
            if (is_numeric($name) && is_string($exp)) {
                $name = str_replace('.','_',$exp);
            }

            if (!($exp instanceof ExpressionInterface)) {
                throw new \InvalidArgumentException("Values can be only of ExpressionInterface");
            }
            $this->expressions[$name] = $exp;
        }
    }

    /**
     * @see \Qmaker\Linq\Expression\ExpressionInterface::__invoke()
     */
    function __invoke($value, \Iterator $iterator = null)
    {
        $params = func_get_args();

        return array_map(function (callable $exp) use ($params) {
                return call_user_func_array($exp, $params);
            }, $this->expressions);
    }

    /**
     * @return ExpressionInterface[]
     */
    public function getItems() {
        return $this->expressions;
    }
}