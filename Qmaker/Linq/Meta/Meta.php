<?php

namespace Qmaker\Linq\Meta;

use Qmaker\Linq\Iterators\ProductIterator;

class Meta implements \IteratorAggregate
{
    /**
     * References to the branches. The key is the name of the branch
     * @var Stream[]
     */
    public $streams;

    /**
     * Current stream
     * @var Stream
     */
    protected $current = null;

    private $defaultName = 'a';

    /**
     * Constructor
     */
    public function __construct() {
        $this->streams = [];
    }

    /**
     * Generate default name for the stream
     * @return string
     */
    public function getDefaultName() {
        $name = $this->defaultName;
        $this->defaultName = chr(ord($name)+1);
        return $name;
    }

    /**
     * @return Stream
     */
    public function getCurrent() {
        return $this->current;
    }

    /**
     * @param Stream $stream
     */
    public function setCurrent(Stream $stream) {
        if ($this->getStream($stream->getName()) === $stream) {
            $this->current = $stream;
        }
    }

    /**
     * Get stream by name
     * @param string $name
     * @return null|Stream
     */
    public function getStream($name) {
        foreach ($this->streams as $stream) {
            /** @var Stream $stream */
            if ($stream->getName() == $name) {
                return $stream;
                break;
            }
        }

        return null;
    }

    /**
     * @param Stream $stream
     * @return Stream
     */
    public function addStream($stream) {
        if ($this->getStream($stream->getName()) == null) {
            $this->streams[] = $stream;
            $this->current = $stream;
            return $stream;
        } else {
            return null;
        }
    }

    /**
     * @param Stream[] $streams
     * @return Stream
     */
    public function mergeStreams(array $streams) {
        $stream = new Stream('');
        $name = '';
        foreach ($streams as $item) {
            if ($this->getStream($stream->getName()) == null) {
                $stream->addChild($item);
                $name = $name . $item->getName();
            }
        }
        $stream->setName($name);
        return $this->addStream($stream);
    }

    /**
     * Get iterator for the stream
     * @see \IteratorAggregate::getIterator()
     */
    public function getIterator() {

        // collect non activated streams
        $streams = [];
        foreach ($this->streams as $stream) {
            /** @var Stream $stream */
            if (!$stream->isActivated()) {
                $streams[$stream->getName()] = $stream;
            }
        }

        if (count($streams) == 1) {
            return reset($streams)->getIterator();
        } elseif (count($streams) > 1) {
            $iterator = new ProductIterator();
            array_walk($streams, function (Stream $stream, $name) use ($iterator) {
                    $iterator->attachIterator($stream->getIterator(), $name);
            });
            return $iterator;
        } else {
            throw new \BadMethodCallException("Something is completely wrong. There is no non-activated stream");
        }
    }
}