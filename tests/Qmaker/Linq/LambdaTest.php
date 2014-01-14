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
        $l = Lambda::and_(Lambda::v()->gt(1), Lambda::v()->lt(2))->eq(true);
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

    public function testComplex()
    {
        $l = Lambda::complex([
            'x1' => Lambda::v(),
            'x2' => Lambda::v()->mult(2),
            'x3' => Lambda::c(1)
        ]);
        $this->assertEquals([ 'x1'=>1.5, 'x2'=>3.0, 'x3'=>1 ], $l(1.5));
    }
}