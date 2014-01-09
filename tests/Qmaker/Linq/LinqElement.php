<?php

namespace Qmaker\Linq\Recursive;

use Qmaker\Linq\Linq;

class LinqElementTest extends \PHPUnit_Framework_TestCase {
    protected $arrayNumeric = array (1, 2, 3, 4, 5);
    protected $arrayEmpty = array ();

    public function testFirstLastPosition() {
        $res = Linq::from($this->arrayNumeric);
        $this->assertEquals(1, $res->first());
        $this->assertEquals(5, $res->last());
        $this->assertEquals(4, $res->elementAt(3));
    }

    /**
     * @expectedException \OutOfRangeException
     */
    public function testEmptyIterator() {
        $res = Linq::from($this->arrayEmpty);
        $res->first();
    }

    /**
     * @expectedException \OutOfRangeException
     */
    public function testSingle() {
        $res = Linq::from([1, 2]);
        $res->single();
    }
}