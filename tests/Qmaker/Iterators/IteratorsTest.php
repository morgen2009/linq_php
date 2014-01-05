<?php

namespace Qmaker\Iterators;


class IteratorsTest extends \PHPUnit_Framework_TestCase {
    public function testCallbackFilterIterator() {
        $data = [1, 2, 3, 4, 5];

        $result = new CallbackFilterIterator(new \ArrayIterator($data), function ($item) {
            return $item > 1;
        });
        $result->addCallback(function ($item) {
            return $item < 5;
        });

        $this->assertEquals([2, 3, 4], iterator_to_array($result, false));
    }

    public function testCallbackIterator() {
        $fibonacci = function () {
            $f2 = 0;
            $f1 = 1;
            return function (\Iterator $iterator) use (&$f2, &$f1) {
                if ($iterator->key() == 0) {
                    return $f2;
                } elseif ($iterator->key() == 1) {
                    return $f1;
                } else {
                    $f = $f1 + $f2;
                    $f2 = $f1;
                    $f1 = $f;
                    return $f;
                }
            };
        };
        $iterator = new \LimitIterator(new CallbackIterator($fibonacci), 0, 10);
        $this->assertEquals([0, 1, 1, 2, 3, 5, 8, 13, 21, 34], iterator_to_array($iterator, false));
    }

    public function testDistinctIterator() {
        $data = [1, 2, 2, 4, 5, 6, 6];
        $iterator = new DistinctIterator(new \ArrayIterator($data), function ($item) {
            return $item;
        });
        $this->assertEquals([1, 2, 4, 5, 6], iterator_to_array($iterator, false));
    }

    public function testExceptIterator() {
        $data1 = [1, 2, 2, 4, 5, 6, 6];
        $data2 = [3, 4, 2, 7];
        $iterator = new ExceptIterator(new \ArrayIterator($data1), new \ArrayIterator($data2), function ($item) {
            return $item;
        });
        $this->assertEquals([1, 5, 6], iterator_to_array($iterator, false));
    }

    public function testIntersectIterator() {
        $data1 = [1, 2, 2, 4, 5, 6, 6];
        $data2 = [3, 4, 2, 7];
        $iterator = new IntersectIterator(new \ArrayIterator($data1), new \ArrayIterator($data2), function ($item) {
            return $item;
        });
        $iterator->rewind();
        $this->assertEquals([2, 4], iterator_to_array($iterator, false));
    }

    public function testProjectionIterator() {
        $data = [1, 2, 4, 5];
        $iterator = new ProjectionIterator(new \ArrayIterator($data), function ($item) {
            return $item * 2;
        });
        $this->assertEquals([2, 4, 8, 10], iterator_to_array($iterator, false));
    }

    public function testTakeIterator() {
        $data = [1, 2, 1, 4, 5];
        $iterator = new TakeIterator(new \ArrayIterator($data), function ($item) {
            return $item <= 2;
        });
        $this->assertEquals([1, 2, 1], iterator_to_array($iterator, false));
    }

    public function testSkipIterator() {
        $data = [1, 2, 1, 4, 5];
        $iterator = new SkipIterator(new \ArrayIterator($data), function ($item) {
            return $item <= 2;
        });
        $this->assertEquals([4, 5], iterator_to_array($iterator, false));
    }

    public function testProductIterator() {
        $data1 = [1, 2, 3];
        $data2 = [2, 4];

        $iterator = new ProductIterator();
        $iterator->attachIterator(new \ArrayIterator($data1), 'a');
        $iterator->attachIterator(new \ArrayIterator($data2), 'b');

        $this->assertEquals([
            ['a' => 1, 'b' => 2],
            ['a' => 1, 'b' => 4],
            ['a' => 2, 'b' => 2],
            ['a' => 2, 'b' => 4],
            ['a' => 3, 'b' => 2],
            ['a' => 3, 'b' => 4]
        ], iterator_to_array($iterator, false));
    }
}
 