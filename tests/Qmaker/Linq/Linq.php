<?php

namespace Qmaker\Linq;

class LinqTest extends \PHPUnit_Framework_TestCase {

    public function testRange() {
        $iterator = Linq::range(1, 3);
        $this->assertEquals([1,2,3], $iterator->toArray());
    }

    public function testRepeat() {
        $iterator = Linq::repeat('a', 3);
        $this->assertEquals(['a', 'a', 'a'], $iterator->toArray());
    }

    public function testFrom() {
        $iterator = Linq::from([1,2,3]);
        $this->assertEquals([1,2,3], $iterator->toArray());

        $iterator = Linq::from(new \ArrayIterator([1,2,3]));
        $this->assertEquals([1,2,3], $iterator->toArray());
    }

    public function testWhere()
    {
        $iterator = Linq::from([1,2,3,4])->where(function ($item) {
            return $item % 2 == 0;
        });
        $this->assertEquals([2,4], $iterator->toArray());
    }

    public function testOfType()
    {
        $iterator = Linq::from([1,'a',3,4])->ofType('string');
        $this->assertEquals(['a'], $iterator->toArray());
    }

    public function testSkipNumeric()
    {
        $data = [1, 2, 3, 4, 5];
        $iterator = Linq::from($data)->skip(2);
        $this->assertEquals([3,4,5], $iterator->toArray());
    }

    public function testTakeNumeric()
    {
        $data = [1, 2, 3, 4, 5];
        $iterator = Linq::from($data)->take(3);
        $this->assertEquals([1,2,3], $iterator->toArray());
    }

    public function testSkipExpression() {
        $data = [1, 2, 3, 4, 5];
        $iterator = Linq::from($data)->skipWhile(function ($x) { return $x < 3; });
        $this->assertEquals([3,4,5], $iterator->toArray());
    }

    public function testTakeExpression() {
        $data = [1, 2, 3, 4, 5];
        $iterator = Linq::from($data)->takeWhile(function ($x) { return $x < 3; });
        $this->assertEquals([1,2], $iterator->toArray());
    }

    public function testConcat() {
        $a = [1, 2, 3, 4];
        $b = ['x', 'y', 'z'];
        $iterator = Linq::from($a)->concat($b);

        $this->assertEquals([1, 2, 3, 4, 'x', 'y', 'z'], $iterator->toArray());
    }

    public function testEquality() {
        $a = [1, 2, 3, 4];
        $b = [3, 4, 2, 1];
        $c = [4, 3, 1, 3];

        $result = Linq::from($a)->isEqual($b);
        $this->assertEquals(true, $result);

        $result = Linq::from($a)->isEqual($c);
        $this->assertEquals(false, $result);
    }
}