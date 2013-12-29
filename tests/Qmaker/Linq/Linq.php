<?php

namespace Qmaker\Linq;

use Qmaker\Linq\Iterators\ProjectionIterator;

class LinqTest extends \PHPUnit_Framework_TestCase {

    public function testExtension() {
        $data = [1,2,3,4,5];
        Linq::register('twice', function ($factor) {
            return function ($iterator) use ($factor) {
                return new ProjectionIterator($iterator, function ($item) use ($factor) {
                    return $factor*$item;
                });
            };
        });
        $iterator = Linq::from($data, 'x')->twice(2);
        $this->assertEquals([2,4,6,8,10], $iterator->toArray());
    }
}