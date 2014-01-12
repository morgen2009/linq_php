<?php

namespace Qmaker\Linq\Expression;


use Qmaker\Linq\Expression\Operation\OperationInterface;

class Expression {

    /**
     * @var array
     */
    protected $commands;

    public function __construct(array $commands) {
        $this->commands = $commands;
    }

    public function __invoke()
    {
        $stack = new \SplStack();
        $params = func_get_args();

        foreach ($this->commands as $command) {
            if ($command instanceof OperationInterface) {
                $command->compute($stack);
            } elseif (is_callable($command)) {
                $stack->push(call_user_func_array($command, $params));
            } else {
                $stack->push($command);
            }
        }
        if ($stack->count() !== 1) {
            throw new \BadMethodCallException();
        }
        return $stack->pop();
    }
}