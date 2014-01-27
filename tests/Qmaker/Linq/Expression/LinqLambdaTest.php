<?php

namespace Qmaker\Linq\Expression;


class LinqLambdaTest extends \PHPUnit_Framework_TestCase {
    public function testFirst()
    {
        $lambda = Lambda::v()->linq()->first();
        $data = new \ArrayIterator([2, 3, 4]);
        $data->rewind();
        $this->assertEquals(2, $lambda($data));
    }

    public function testSum()
    {
        $lambda = Lambda::v()->linq()->sum();
        $data = new \ArrayIterator([2, 3, 4]);
        $this->assertEquals(9, $lambda($data));
    }

    public function testFirstWithComparison()
    {
        $lambda = Lambda::v()->linq()->first()->add(1);
        $data = new \ArrayIterator([2, 3, 4]);
        $data->rewind();
        $this->assertEquals(3, $lambda($data));
    }
}
 