<?php

namespace Qmaker\Iterators;


use Qmaker\Fixtures\Car;
use Qmaker\Fixtures\CarExample;
use Qmaker\Fixtures\Category;

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

    public function testGroupIterator() {
        $data = CarExample::cars();

        $iterator = new GroupingIterator(new \ArrayIterator($data), function (Car $car) {
            return $car->getCategory()->getId();
        });
        $iterator = new ProjectionIterator($iterator, function (\Iterator $value, \Iterator $iterator) {
            return [
                'group' => $iterator->key(),
                'max' => max(iterator_to_array(new ProjectionIterator($value, function (Car $car) {
                    return $car->getPrice();
                }))),
                'count' => array_sum(iterator_to_array(new ProjectionIterator($value, function () {
                    return 1;
                })))
            ];
        });

        $this->assertEquals([
            ['group' => 1, 'max' => 17000, 'count' => 2],
            ['group' => 2, 'max' => 20000, 'count' => 1],
            ['group' => 3, 'max' => 30000, 'count' => 1]
        ], iterator_to_array($iterator, false));
    }

    public function testJoinIterator() {
        $cars = CarExample::cars();
        $categories = CarExample::categories();

        $index = new IndexIterator(new \ArrayIterator($categories), function (Category $c) {
            return $c->getId();
        });

        $this->assertEquals([1, 2, 3], iterator_to_array(new ProjectionIterator($index, function (Category $c) {
            return $c->getId();
        }), false), 'IndexIterator for categories');

        $iterator = new JoinIterator(new \ArrayIterator($cars), function (Car $c) {
            return $c->getCategory()->getId();
        }, $index);

        $iterator = new ProjectionIterator($iterator, function (array $value) {
            return [
                $value['left']->getTitle(),
                $value['right']->getTitle(),
            ];
        });
        $this->assertEquals([
            ['Opel', 'Low'],
            ['BMW', 'Middle'],
            ['Mercedes', 'High'],
            ['Honda', 'Low'],
        ], iterator_to_array($iterator, false));
    }
}
 