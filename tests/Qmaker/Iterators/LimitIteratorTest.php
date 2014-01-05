<?php

namespace Qmaker\Iterators;

class LimitIteratorTest extends \PHPUnit_Framework_TestCase {
    /**
     * @skiped
     */
    public function testSeekableIterator() {
        $data = [1, 2, 3, 4];

        $object = new \ArrayIterator($data);
        $container = new \stdClass();
        $container->method = null;
        $container->stat = array_combine(['current', 'next', 'rewind', 'valid', 'seek', 'key'], array_pad([], 6, 0));

        $i = $this->getMock('\ArrayIterator');
        $i->expects($this->any())->method($this->callback(function ($name) use ($container) {
            $container->method = $name;
            return array_key_exists($name, $container->stat) !== false;
        }))->will($this->returnCallback(function () use ($object, $container) {
            $container->stat[$container->method]++;
            return call_user_func_array([$object, $container->method], func_get_args());
        }));

        $i = new \LimitIterator($i, 3, 4);
        iterator_to_array($i);

        $this->assertEquals([
            'current' => 1,
            'next'    => 1,
            'rewind'  => 1,
            'valid'   => 2,
            'seek'    => 1,
            'key'     => 1,
        ], $container->stat);
    }
}
 