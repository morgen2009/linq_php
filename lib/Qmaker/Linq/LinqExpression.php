<?php

namespace Qmaker\Linq;

use Qmaker\Linq\Expression\ArrayExpression;
use Qmaker\Linq\Expression\ExpressionInterface;
use Qmaker\Linq\Iterators\OuterIterator;
use Qmaker\Linq\Expression\Exp;
use Qmaker\Linq\Meta\Meta;
use Qmaker\Linq\Meta\MetaAware;
use Qmaker\Linq\Meta\Stream;

class LinqExpression implements ExpressionInterface, Operation\Standard
{
    use MetaAware;

    /* Implementation of standard Linq operations */
    use Recursive\Concatenation;
    use Recursive\Filtering;
    use Recursive\Generation;
    use Recursive\Grouping;
    use Recursive\Joining;
    use Recursive\Partitioning;
    use Recursive\Projection;
    use Recursive\Set;
    use Recursive\Sorting;

    use Recursive\Aggregation;
    use Recursive\Equality;
    use Recursive\Element;
    use Recursive\Quantifier;

    /**
     * Expressions to compute the input iterators
     * @var ExpressionInterface
     */
    protected $expression;

    /**
     * Input iterators for expression
     * @var OuterIterator[]|OuterIterator
     */
    protected $input;

    /**
     * Iterator for expression (will be built once from the meta at the first call)
     * @var \Iterator
     */
    private $iterator;

    /**
     * Constructor
     * @param mixed $expression If an array is given, then each element in it specifies a separated stream
     * @see \Qmaker\Linq\Expression\Exp::instanceFrom
     */
    public function __construct($expression) {
        $this->meta = new Meta();
        $this->expression = Exp::instanceFrom($expression);

        if ($this->expression instanceof ArrayExpression) {
            $this->input = [];
            foreach ($this->expression->getItems() as $name => $item) {
                $this->input[$name] = $input = new OuterIterator();
                $this->meta->addStream(new Stream($name))->addItem(function () use ($input) {
                    return $input;
                });
            }
        } else {
            $this->input = new OuterIterator();
            $input = $this->input;
            $this->meta->addStream(new Stream($this->meta->getDefaultName()))->addItem(function () use ($input) {
                return $input;
            });
        }
    }

    /**
     * Give the callable, which call the linq method
     *
     * @param string $method
     * @param array $params
     * @return callable
     */
    public function apply($method, array $params = []) {
        $linq = $this;
        return function ($value, \Iterator $iterator = null) use ($linq, $method, $params)  {
            $iterator = $linq($value, $iterator);
            return call_user_func_array([Linq::from($iterator), $method], $params);
        };
    }

    /**
     * @see ExpressionInterface::__invoke
     */
    function __invoke($value, \Iterator $iterator = null)
    {
        /** @var callable $expression */
        $expression = $this->expression;
        $value = $expression($value, $iterator);

        if ($this->expression instanceof ArrayExpression) {
            if (!is_array($value)) {
                throw new WrongTypeException($value, "array", "The value for LinqExpression");
            }
            foreach ($value as $name => $item) {
                $this->input[$name]->setInnerIterator($item);
            };
        } else {
            if (is_array($value)) {
                $value = new \ArrayIterator($value);
            }
            if (!($value instanceof \Iterator)) {
                throw new WrongTypeException($value, "iterator", "The value for LinqExpression");
            }
            $this->input->setInnerIterator($value);
        }

        // build iterator for meta
        if (empty($this->iterator)) {
            $this->iterator = $this->meta->getIterator();
        }

        return $this->iterator;
    }
}
