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
        $this->assertEquals('v2', $cache->get('t2'));
        $t3 = ['a'=>1, 'b'=>2, 'c'=>3];
        $cache->set('t3', $t3);
        $this->assertEquals($t3, $cache->get('t3'));
        $t4 = ['b'=>2, 'c'=>3];
        $cache->set('t3', $t4);
        $this->assertEquals($t4, $cache->get('t3'));
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
