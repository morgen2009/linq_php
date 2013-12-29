<?php

namespace Qmaker\Linq\Expression;

use Qmaker\Linq\Iterators\RelationInterface;

class IteratorPathExpression extends PathExpression
{
    protected $iterator;

    /**
     * @var string
     */
    protected $class;

    /**
     * @param string $path
     * @param string $class
     * @throws \InvalidArgumentException
     */
    public function __construct($path, $class = '\Iterator') {
        parent::__construct($path);
        $this->class = $class;
        $this->iterator = null;
    }

    /**
     * @see \Qmaker\Linq\Expression\ExpressionInterface::__invoke()
     */
    function __invoke($value, \Iterator $iterator = null)
    {
        if (!empty($this->iterator)) {
            return parent::__invoke($this->iterator);
        }

        // find suitable iterator, which the path can be resolved on
        // try current iterator
        if ($iterator instanceof $this->class) {
            $value = parent::__invoke($iterator);
            if (!empty($this->callbacks)) {
                $this->iterator = $iterator;
                return $value;
            }
        }

        // try related iterators
        if ($iterator instanceof RelationInterface) {
            foreach ($iterator->getRelatedIterators() as $item) {
                $value = $this->__invoke($value, $item);
                if (!empty($this->callbacks)) {
                    return $value;
                }
            }
        }

        // try inner iterator
        if ($iterator instanceof \OuterIterator) {
            return self::__invoke($value, $iterator->getInnerIterator());
        }

        // suitable iterator not found
        return null;
    }
}