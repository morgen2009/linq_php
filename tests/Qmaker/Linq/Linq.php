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
}