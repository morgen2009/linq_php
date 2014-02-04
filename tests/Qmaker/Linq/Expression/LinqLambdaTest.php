<?php

namespace Qmaker\Linq\Expression;


class LinqLambdaTest extends \PHPUnit_Framework_TestCase {
    public function testFirst()
    {
        $lambda = Lambda::define()->x()->linq()->first();
        $data = new \ArrayIterator([2, 3, 4]);
        $data->rewind();
        $this->assertEquals(2, $lambda($data));
    }

    public function testSum()
    {
        $lambda = Lambda::define()->x()->linq()->sum();
        $data = new \ArrayIterator([2, 3, 4]);
        $this->assertEquals(9, $lambda($data));
    }

    public function testFirstWithComparison()
    {
        $lambda = Lambda::define()->x()->linq()->first()->add(1);
        $data = new \ArrayIterator([2, 3, 4]);
        $data->rewind();
        $this->assertEquals(3, $lambda($data));
    }
}
 