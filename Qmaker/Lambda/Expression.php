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

            // export expression
            $this->dataToLevel();
            $this->levelToData();

            if (!empty($this->levels)) {
                $this->dataToLevel();
            }
        }
        return true;
    }

    protected function levelToData()
    {
        // extract level
        $item = array_pop($this->levels);

        // convert it into postfix notation and store in data
        array_push($item, $item[self::OFFSET_COUNT]);
        array_push($item, $item[self::OFFSET_OPERATOR]);
        $this->data = array_slice($item, 2);
        $this->dataCount = empty($this->data) ? 0 : 1;
    }

    protected function dataToLevel()
    {
        $item =& $this->levels[count($this->levels)-1];
        $operator = $item[self::OFFSET_OPERATOR];
        if ($operator instanceof ParameterAwareInterface) {
            /** @var ParameterAwareInterface $operator */
            foreach ($this->data as $value) {
                $operator->addParameter($value);
            }
        } else {
            $item = array_merge($item, $this->data);
            $item[self::OFFSET_COUNT] += $this->dataCount;
        }
        $this->data = [];
        $this->dataCount = 0;
    }

    /**
     * @throws \BadMethodCallException
     */
    protected function collapseAll()
    {
        for ($i = count($this->levels)-1; $i>=0; $i--) {
            $this->dataToLevel();
            $this->levelToData();
        }
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
            array_push($this->levels, array_merge([$operator, $this->dataCount], $this->data));
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
        $this->collapseAll();
        $this->levels = $this->groups->pop();

        return $this;
    }

    /**
     * Prepare expression to be computed and mark it as read-only
     */
    public function compile() {
        if (!$this->isCompiled) {
            $this->collapseAll();
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
        $this->compile();
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