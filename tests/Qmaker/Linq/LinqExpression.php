<?php

namespace Qmaker\Linq\Recursive;

use Qmaker\Linq\Linq;

class LinqExpressionTest extends \PHPUnit_Framework_TestCase {

    protected $arrayNested = array (
        ['a' => 'group 1', 'b' => [1,2]],
        ['a' => 'group 2', 'b' => [4,67]],
        ['a' => 'group 3', 'b' => [1,1]],
        ['a' => 'group 4', 'b' => [3,4]],
        ['a' => 'group 5', 'b' => [5,6]]
    );

    public function testLinqInnerExpression() {
        $res = Linq::from($this->arrayNested)->select([
            'ax' => 'a',
            'bx' => Linq::exp('b')->first()
        ]);
        $res = iterator_to_array($res);
        $this->assertEquals([
            ['ax'=>'group 1', 'bx' => 1],
            ['ax'=>'group 2', 'bx' => 4],
            ['ax'=>'group 3', 'bx' => 1],
            ['ax'=>'group 4', 'bx' => 3],
            ['ax'=>'group 5', 'bx' => 5],
        ], $res);
    }
}