<?php

namespace Qmaker\Linq\Recursive;

use Qmaker\Linq\Linq;

class PartitioningTest extends \PHPUnit_Framework_TestCase {

    public function testSkipNumeric() {
        $data = [1, 2, 3, 4, 5];
        $iterator = Linq::from($data)->skip(2);
        $result = iterator_to_array($iterator, false);
        $this->assertEquals([3,4,5], $result);
    }

    public function testTakeNumeric() {
        $data = [1, 2, 3, 4, 5];
        $iterator = Linq::from($data)->take(3);
        $result = iterator_to_array($iterator, false);
        $this->assertEquals([1,2,3], $result);
    }

    public function testSkipExpression() {
        $data = [1, 2, 3, 4, 5];
        $iterator = Linq::from($data)->skipWhile(function ($x) { return $x < 3; });
        $result = iterator_to_array($iterator, false);
        $this->assertEquals([3,4,5], $result);
    }

    public function testTakeExpression() {
        $data = [1, 2, 3, 4, 5];
        $iterator = Linq::from($data)->takeWhile(function ($x) { return $x < 3; });
        $result = iterator_to_array($iterator, false);
        $this->assertEquals([1,2], $result);
    }
}