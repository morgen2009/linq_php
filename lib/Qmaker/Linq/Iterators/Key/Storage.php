<?php

namespace Qmaker\Linq\Iterators\Key;

class Storage
{
    /**
     * @var KeyInterface|null
     */
    protected $key;

    /**
     * @var mixed[]|KeyValuePair[]
     */
    protected $data;

    /**
     * @var bool
     */
    protected $withValues;

    const WITH_VALUES = 1;
    const WITHOUT_VALUES = 0;

    /**
     * Constructor
     */
    public function __construct($flag = self::WITHOUT_VALUES) {
        $this->key = null;
        $this->data = [];
        $this->withValues = $flag & self::WITH_VALUES;
    }

    /**
     * @param KeyInterface $key
     */
    public function addKey(KeyInterface $key) {
        if (empty($this->key)) {
            $this->key = $key;
        } elseif ($this->key instanceof ComplexKey) {
            $this->key->add($key);
        } elseif ($this->key instanceof KeyInterface) {
            $newKey = new ComplexKey();
            $newKey->add($this->key);
            $newKey->add($key);
            $this->key = $newKey;
        }
    }

    /**
     * @return KeyInterface|null
     */
    public function getKey() {
        return $this->key;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Add key into storage
     * @param mixed $key
     * @return int|bool
     */
    public function push($key) {
        $position = $this->search($key);
        if ($position >= 0) {
            return $position;
        }
        $position = -$position - 1;
        $this->data = array_merge(
            array_slice($this->data, 0, $position),
            [ $key ],
            array_slice($this->data, $position, null)
        );

        return -($position+1);
    }

    /**
     * Delete key from storage
     * @param mixed $key
     * @return mixed|null
     */
    public function pop($key) {
        $position = $this->search($key);
        if ($position < 0) {
            return null;
        }

        $result = $this->data[$position];
        if ($position == 0) {
            array_shift($this->data);
        } else {
            $this->data = array_merge(
                array_slice($this->data, 0, $position-1),
                array_slice($this->data, $position, null)
            );
        }

        return $result;
    }

    /**
     * Compute key of values and load them into storage
     * @param array $values
     * @return bool
     */
    public function load(array $values) {
        $me = $this;

        $this->data = array_map(function ($value) use ($me) {
            return $me->createKey($value);
        }, $values);

        usort($this->data, function ($x, $y) use ($me) {
            return $me->compare($x, $y);
        });
    }

    /**
     * @param mixed $value
     * @return mixed|KeyValuePair
     */
    public function createKey($value) {
        $key = $this->key->compute($value);
        if ($this->withValues) {
            return new KeyValuePair($key, $value);
        } else {
            return $key;
        }
    }

    /**
     * @param mixed $value
     * @return int|bool
     */
    public function searchByValue($value) {
        $key = $this->createKey($value);

        return $this->searchByKey($key);
    }

    /**
     * @param mixed|KeyValuePair $key
     * @return int|bool
     */
    public function searchByKey($key) {
        $position = $this->search($key);

        return $position >= 0 ? $position : false;
    }

    /**
     * @param $x
     * @param $y
     * @return int
     */
    public function compare($x, $y) {
        if ($x instanceof KeyValuePair) {
            $x = $x->key;
        }
        if ($y instanceof KeyValuePair) {
            $y = $y->key;
        }
        return $this->key->compare($x, $y);
    }

    /**
     * @param array|mixed $key
     * @return int
     */
    protected function search($key) {
        $low = 0;
        $high = count($this->data) - 1;

        while ($low <= $high) {
            $mid = (int)(($low + $high) / 2);
            $midVal = $this->data[$mid];

            switch ($this->compare($midVal, $key)) {
                case 1: {
                    $high = $mid - 1;
                    break;
                }
                case -1: {
                    $low = $mid + 1;
                    break;
                }
                default : {
                    return $mid; // key found
                }
            }
        }
        return -($low + 1);  // key not found.
    }
}