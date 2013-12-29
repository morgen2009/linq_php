<?php

namespace Qmaker\Linq\Expression;

class CallbackExpression implements ExpressionInterface
{
    /**
     * @var callable
     */
    protected $callback;

    /**
     * @var int
     */
    protected $argCount;

    /**
     * @param callable $callback
     * @throws \InvalidArgumentException
     */
    public function __construct(callable $callback) {
        $this->callback = $callback;

        // store the number of parameters of the callback
        $reflection = new \ReflectionFunction($callback);
        $params = $reflection->getParameters();
        $this->argCount = count($params);

        if ($this->argCount == 0) {
            throw new \InvalidArgumentException("Closure has to have at least one parameter");
        }
    }

    /**
     * @see \Qmaker\Linq\Expression\ExpressionInterface::__invoke()
     */
    function __invoke($value, \Iterator $iterator = null)
    {
        if ($this->argCount == 1) {
            return call_user_func($this->callback, $value);
        } else {
            return call_user_func_array($this->callback, func_get_args());
        }
    }
}