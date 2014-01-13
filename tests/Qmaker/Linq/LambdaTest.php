<?php

namespace Qmaker\Linq;


use Qmaker\Fixtures\CarExample;
use Qmaker\Linq\Expression\Lambda;
use Qmaker\Linq\Expression\LambdaInstance;

class LambdaTest extends \PHPUnit_Framework_TestCase {
    public function testVariable()
    {
        // equivalent for (x) => x + x*2 + 3
        $l = (new LambdaInstance())->v()->add()->v()->mult(2)->add()->c(3);
        $this->assertEquals(6, $l(1), 'LambdaInstance');

        $l = Lambda::v()->add()->v()->mult(2)->add()->c(3);
        $this->assertEquals(6, $l(1), 'Lambda');
    }

    public function testLogicalOperation()
    {
        $l = Lambda::_and(Lambda::v()->gt(1), Lambda::v()->lt(2))->eq(true);
        $this->assertEquals(true, $l(1.5));
    }

    public function testPathOperation()
    {
        $cars = CarExample::cars();
        $l = Lambda::v('price');
        $this->assertEquals(16000, $l($cars[0]));

        $l = Lambda::v()->item('price');
        $this->assertEquals(16000, $l($cars[0]));

        $l = Lambda::v()->getPrice();
        $this->assertEquals(16000, $l($cars[0]));
    }
}
 