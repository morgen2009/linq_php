<?php

namespace Qmaker\Linq;


use Qmaker\Linq\Expression\Lambda;
use Qmaker\Linq\Expression\LambdaInstance;

class LambdaTest extends \PHPUnit_Framework_TestCase {
    public function testVariable()
    {
        // equivalent for (x) => x + x*2 + 3
        $l = (new LambdaInstance())->v()->add()->v()->mult(2)->add()->c(3);
        $this->assertEquals(6, $l(1));

        $l = Lambda::v()->add()->v()->mult(2)->add()->c(3);
        $this->assertEquals(6, $l(1));
    }
}
 