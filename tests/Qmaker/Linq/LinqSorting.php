<?php

namespace Qmaker\Linq;

class LinqSortingTest extends \PHPUnit_Framework_TestCase {

    public function testNoCriteria() {
        $data = [1, 2, 5, 4, 3];
        $iterator = Linq::from($data)->order();
        $this->assertEquals([1,2,3,4,5], $iterator->toArray());
    }

    public function testReverse() {
        $data = [1, 2, 5, 4, 3];
        $iterator = Linq::from($data)->reverse();
        $this->assertEquals([3,4,5,2,1], $iterator->toArray());
    }

    public function testOneCriteria() {
        $data = [1, 2, 5, 4, 3];
        $iterator = Linq::from($data)->orderBy(function ($value) {
            return $value;
        });
        $this->assertEquals([1,2,3,4,5], $iterator->toArray());

        $iterator = Linq::from($data)->orderByDescending(function ($value) {
            return $value;
        });
        $this->assertEquals([5,4,3,2,1], $iterator->toArray());
    }

    public function testMoreCriteria() {
        $data = [1, 2, 5, 4, 3];
        $iterator = Linq::from($data)->orderByDescending(function ($value) {
            return $value % 2;
        })->thenBy(function ($value) {
            return $value;
        });
        $this->assertEquals([1,3,5,2,4], $iterator->toArray());
    }
}