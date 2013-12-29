<?php

namespace Qmaker\Linq\Meta;


class Stream implements \IteratorAggregate
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var Stream[]
     */
    protected $children;

    /**
     * @var callable[]
     */
    protected $items;

    /**
     * @var bool
     */
    protected $active = false;

    /**
     * Constructor
     */
    public function __construct($name) {
        $this->name = $name;
        $this->items = [];
        $this->children = [];
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @param Stream $stream
     */
    public function addChild(Stream $stream) {
        $this->children[] = $stream;
        $stream->setActivated();
    }

    /**
     * @return Stream[]
     */
    public function getChildren() {
        return $this->children;
    }

    /**
     * @param callable $item
     */
    public function addItem(callable $item) {
        $this->items[] = $item;
    }

    /**
     * @return callable[]
     */
    public function getItems() {
        return $this->items;
    }

    /**
     * @return bool
     */
    public function isActivated()
    {
        return $this->active;
    }

    /**
     *
     */
    public function setActivated()
    {
        $this->active = true;
    }

    /**
     * @see \IteratorAggregate::getIterator
     */
    public function getIterator()
    {
        $input = [];
        foreach ($this->children as $stream) {
            /** @var Stream $stream */
            $input[] = $stream->getIterator();
        }

        foreach ($this->items as $item) {
            $input = $this->buildIterator($item, $input);
        }

        return $input;
    }

    protected function buildIterator(callable $callback, $input) {

        if (is_array($input)) {
            if (count($input) > 1) {
                $reflection = new \ReflectionFunction($callback);
                if ($reflection->getNumberOfParameters() == 1) {
                    return call_user_func($callback, $input);
                }
            }
            return call_user_func_array($callback, $input);
        }

        return call_user_func($callback, $input);
    }
}
