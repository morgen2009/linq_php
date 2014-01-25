<?php

namespace Qmaker\Lambda;


class Expression {

    /**
     * Constants
     */
    const OFFSET_OPERATOR = 0;
    const OFFSET_COUNT    = 1;

    /**
     * @var array[] Each element of array is array of the following structure [instance of OperatorInterface, number of arguments, ... exported expression in postfix notation ...]
     */
    protected $levels = [];

    /**
     * @var mixed
     */
    protected $data = [];

    /**
     * @var int
     */
    protected $dataCount = 0;

    /**
     * @var \SplStack
     */
    protected $groups;

    /**
     * @var boolean
     */
    protected $isCompiled = false;

    /**
     * Constructor
     */
    public function __construct() {
    }

    /**
     * @param OperatorInterface $operatorNew
     * @return bool True stands for adding new operator, false stands for skipping
     */
    protected function collapseLevels(OperatorInterface $operatorNew) {
        while (!empty($this->levels)) {
            /** @var array $item */
            $item =& $this->levels[count($this->levels)-1];

            /** @var OperatorInterface $operator */
            $operator = $item[self::OFFSET_OPERATOR];
            if ($operator->getPriority() < $operatorNew->getPriority()) {
                return true;
            }

            /** @var int $count */
            $count = $item[self::OFFSET_COUNT];
            if ($operator->getPriority() === $operatorNew->getPriority() && $count < $operator->getMaxCount()) {
                return false;
            }

            // export and add to the previous level
            $export = array_slice($item, 2);
            array_push($export, $count);
            array_push($export, $operator);

            array_pop($this->levels);
            if (empty($this->levels)) {
                $this->data = $export;
            } else {
                $item =& $this->levels[count($this->levels)-1];
                $item = array_merge($item, $export);
                $item[self::OFFSET_COUNT]++;
            }
        }
        return true;
    }

    /**
     * @throws \BadMethodCallException
     * @return array
     */
    protected function collapseAll()
    {
        $export = $this->data;
        $count = $this->dataCount;

        for ($i = count($this->levels)-1; $i>=0; $i--) {
            $item = $this->levels[$i];

            $item = array_merge($item, $export);
            array_push($item, $item[self::OFFSET_COUNT]+$count);
            array_push($item, $item[self::OFFSET_OPERATOR]);

            $export = array_slice($item, 2);
            $count = 1;
            unset($this->levels[$i]);
        }

        $this->data = [];
        $this->dataCount = 0;

        return $export;
    }

    /**
     * Add data to expression
     * @param callable|mixed $value
     * @throws \BadMethodCallException
     * @return $this
     */
    public function addData($value) {
        if ($this->isCompiled) {
            throw new \BadMethodCallException("Read-only expression can not be modified");
        }
        array_push($this->data, $value);
        $this->dataCount++;
        return $this;
    }

    /**
     * Add operator to expression
     * @param OperatorInterface $operator
     * @throws \BadMethodCallException
     * @return $this
     */
    public function addOperator(OperatorInterface $operator) {
        if ($this->isCompiled) {
            throw new \BadMethodCallException("Read-only expression can not be modified");
        }
        if ($this->collapseLevels($operator)) {
            array_push($this->levels, array_merge([$operator, 1], $this->data));
            $this->data = [];
            $this->dataCount = 0;
        }
        return $this;
    }

    /**
     * Add opening bracket "("
     * @throws \BadMethodCallException
     * @return $this
     */
    public function with() {
        if ($this->isCompiled) {
            throw new \BadMethodCallException("Read-only expression can not be modified");
        }
        if (empty($this->groups)) {
            $this->groups = new \SplStack();
        }
        $this->groups->push($this->levels);
        $this->levels = [];
        $this->data = [];
        $this->dataCount = 0;
        return $this;
    }

    /**
     * Add closing bracket ")"
     * @throws \BadMethodCallException
     * @return $this
     */
    public function end() {
        if ($this->isCompiled) {
            throw new \BadMethodCallException("Read-only expression can not be modified");
        }
        if (empty($this->groups) || $this->groups->isEmpty()) {
            throw new \BadMethodCallException('Opening bracket is missing');
        }
        $this->data = $this->collapseAll();
        $this->dataCount = empty($this->data) ? 0 : 1;
        $this->levels = $this->groups->pop();

        return $this;
    }

    /**
     * Prepare expression to be computed and mark it as read-only
     */
    public function compile() {
        if (!$this->isCompiled) {
            $this->data = $this->collapseAll();
            $this->dataCount = 1;
            $this->isCompiled = true;
        }
    }

    /**
     * Get compiled expression in postfix notation
     * @return mixed|null
     */
    public function getCompiledData() {
        return $this->isCompiled ? $this->data : null;
    }

    /**
     * Compute expression
     * @return mixed
     * @throws \BadMethodCallException
     */
    public function __invoke()
    {
        $stack = [];
        $params = func_get_args();

        foreach ($this->data as $command) {
            if ($command instanceof OperatorInterface) {
                $command->apply($stack);
            } elseif (is_callable($command)) {
                array_push($stack, call_user_func_array($command, $params));
            } else {
                array_push($stack, $command);
            }
        }
        if (count($stack) !== 1) {
            throw new \BadMethodCallException('Stack has more than 1 values or empty');
        }
        return end($stack);
    }
}