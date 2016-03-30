<?php

/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/3/14
 * Time: 13:33
 */
class StorageTest extends PHPUnit_Framework_TestCase
{

    public static function setUpBeforeClass()
    {
        require '../bootstrap/init.php';
    }

    public function testCache()
    {
        $cache = getCache();
        $cache->set('t1', 'v1')->set('t2', 'v2');
        $this->assertTrue($cache->has('t1'));
        $cache->delete('t1');
        $this->assertFalse($cache->has('t1'));
        $this->assertEquals('v2', $cache->get('t2')->get());
        $cache->set('t3', ['a'=>1, 'b'=>2, 'c'=>3]);
        $this->assertEquals(2, $cache->get('t3')->get('b'));
        $this->assertTrue($cache->get('t3')->has('c'));
        $a = $cache->get('t3');
        $a->delete('a');
        $cache->set('t3', $a);
        $this->assertEquals(['b'=>2, 'c'=>3], $cache->get('t3')->get(null));
    }

    public function testOldValue()
    {
        $ov = getOValue();
        $ov->addOld('a1', 'b1')->addOldOnce('a2', 'b2');
        $this->assertEquals('b1', $ov->getOld('a1'));
        $this->assertEquals('b1', $ov->getOld('a1'));
        $this->assertEquals('x', $ov->getOld('a3', 'x'));
        $this->assertEquals('b2', $ov->getOldOnce('a2'));
        $this->assertEquals('', $ov->getOldOnce('a2'));
    }
}
