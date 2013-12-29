<?php

namespace Qmaker\Linq;

use Qmaker\Linq\Iterators\LazyIterator;
use Qmaker\Linq\Meta\Meta;
use Qmaker\Linq\Meta\MetaAware;

class LinqExecute extends LazyIterator implements Operation\Standard
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

    use Recursive\Execute\Aggregation;
    use Recursive\Execute\Equality;
    use Recursive\Execute\Element;
    use Recursive\Execute\Quantifier;

    /**
     * Constructor
     */
    public function __construct() {
        $this->meta = new Meta();
    }

    /**
     * Get name
     * @return string
     */
    public function getName() {
        $result = '';
        foreach ($this->meta->streams as $stream) {
            if (!$stream->isActivated()) {
                $result .= $stream->getName();
            }
        }
        return $result;
    }

    /**
     * @see \Qmaker\Linq\Iterators\LazyIterator::build
     */
    protected function build() {
        return $this->meta->getIterator();
    }

    /**
     * Export iterator to array
     * @return array
     */
    public function toArray() {
        return iterator_to_array($this->getInnerIterator(), false);
    }

    /**
     * Call extensions
     * @param $name
     * @param $arguments
     * @throws \BadMethodCallException
     * @return $this
     */
    public function __call($name, $arguments) {
        $extension = Linq::getExtension($name);
        if (empty($extension)) {
            throw new \BadMethodCallException("The extension {$name} is not found");
        }

        $callback = call_user_func_array($extension, $arguments);
        if (is_callable($callback)) {
            $this->meta->getCurrent()->addItem($callback);

        } else {
            throw new \BadMethodCallException("The extension returns the wrong result");
        }

        return $this;
    }
}
