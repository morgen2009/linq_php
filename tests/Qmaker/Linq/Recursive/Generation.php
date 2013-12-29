<?php

namespace Qmaker\Linq\Recursive;

use Qmaker\Linq\Linq;

class GenerationTest extends \PHPUnit_Framework_TestCase {

    public function testFrom() {
        $data = [1,2,3,4];
        $result = Linq::from($data)->toArray();
        $this->assertEquals($data, $result);
    }

    public function testRange() {
        $data = [1,2,3,4];
        $result = Linq::range(1, 4)->toArray();
        $this->assertEquals($data, $result);
    }

    public function testRepeat() {
        $data = [1,1,1,1];
        $result = Linq::repeat(1, 4)->toArray();
        $this->assertEquals($data, $result);
    }

    public function testFromCallable() {
        $fibonacci = function () {
            $f2 = 0;
            $f1 = 1;
            return function ($offset) use (&$f2, &$f1) {
                if ($offset == 0) {
                    return $f2;
                } elseif ($offset == 1) {
                    return $f1;
                } elseif ($offset >= 12) {
                    throw new \OutOfBoundsException();
                } else {
                    $f = $f1 + $f2;
                    $f2 = $f1;
                    $f1 = $f;
                    return $f;
                }
            };
        };

        $result = Linq::from($fibonacci);
        $result = iterator_to_array($result);
        $this->assertEquals([0,1,1,2,3,5,8,13,21,34,55,89], $result);
    }
}
 