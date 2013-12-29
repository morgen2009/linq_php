<?php

namespace Qmaker\Linq\Recursive;

use Qmaker\Linq\Linq;

class ConcatenationTest extends \PHPUnit_Framework_TestCase {

    public function testConcat() {
        $a = [1, 2, 3, 4];
        $b = ['x', 'y', 'z'];
        $result = Linq::from($a)->concat($b);
        $result = iterator_to_array($result, false);

        $this->assertEquals([1, 2, 3, 4, 'x', 'y', 'z'], $result);
    }

    public function testConcatWithEmpty() {
        $a = [1, 2, 3, 4];
        $b = [];
        $result = Linq::from($a)->concat($b);
        $result = iterator_to_array($result, false);

        $this->assertEquals($a, $result);
    }
}