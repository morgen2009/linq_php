<?php

namespace Qmaker\Linq\Expression;


use Qmaker\Fixtures\CarExample;

class LambdaTest extends \PHPUnit_Framework_TestCase {
    public function testVariable()
    {
        // equivalent for (x) => x + x*2 + 3
        $l = (new Lambda())->x()->add()->x()->mult(2)->add()->c(3);
        $this->assertEquals(6, $l(1), 'LambdaInstance');

        $l = Lambda::define()->x()->add()->x()->mult(2)->add()->c(3);
        $this->assertEquals(6, $l(1), 'Lambda');
    }

    public function testLogicalOperation()
    {
        $l = Lambda::define()->with()->x()->gt(1)->and_()->x()->lt(2)->end()->eq(true);
        $this->assertEquals(true, $l(1.5));
    }

    public function testPathOperation()
    {
        $cars = CarExample::cars();
        $l = Lambda::define()->v('price');
        $this->assertEquals(16000, $l($cars[0]));

        $l = Lambda::define()->x()->get('price');
        $this->assertEquals(16000, $l($cars[0]));

        $l = Lambda::define()->x()->getPrice();
        $this->assertEquals(16000, $l($cars[0]));
    }

    public function testComplex()
    {
        $l = Lambda::define()->complex([
            'x1' => Lambda::define()->x(),
            'x2' => Lambda::define()->x()->mult(2),
            'x3' => Lambda::define()->c(1)
        ]);
        $this->assertEquals([ 'x1'=>1.5, 'x2'=>3.0, 'x3'=>1 ], $l(1.5));
    }

    public function testLike()
    {
        $l = Lambda::define()->x()->like('hello');
        $this->assertEquals(true, $l('1 hello, 1'), 'strstr');

        $l = Lambda::define()->x()->like('%hello');
        $this->assertEquals(true, $l('ww hello'), 'regexp');
    }
}