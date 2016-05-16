<?php

/**
 * Created by PhpStorm.
 * User: wwt
 * Date: 2016/5/7 0007
 * Time: 下午 10:23
 */
class DataBaseTest extends PHPUnit_Framework_TestCase
{

    public static function setUpBeforeClass()
    {
        require '../bootstrap/init.php';
    }

    public function testMedooPool()
    {
        $conn = new \Flight2wwu\Component\Database\MedooPool();
        $conn->reconnect();
        $db = $conn->getConnection();
        $db->debug = true;
        $re = $db->execute("create table t1 (id int, name text)");
        $this->assertEquals(0, $re);
        $data = [
            ['id'=>1, 'name'=>'name1'],
            ['id'=>2, 'name'=>'name2'],
            ['id'=>3, 'name'=>'name3'],
        ];
        $re = $db->insert('t1', $data);
        $this->assertEquals(3, $re);
        $re = $db->select('t1', '*');
        $this->assertEquals($data, $re);
        $re = $db->select('t1', ['id', 'name'], ['id'=>1]);
        $this->assertEquals([['id'=>1, 'name'=>'name1']], $re);
        $re = $db->get('t1', '*', ['name'=>'name2']);
        $this->assertEquals(['id'=>2, 'name'=>'name2'], $re);
        $re = $db->get('t1', 'name', ['id'=>3]);
        $this->assertEquals('name3', $re);
        $re = $db->count('t1');
        $this->assertEquals(3, $re);
        $re = $db->count('t1', ['id[>]'=>1]);
        $this->assertEquals(2, $re);
        $re = $db->has('t1', ['name'=>'name2']);
        $this->assertTrue($re);
        $re = $db->has('t1', ['name'=>'name4']);
        $this->assertFalse($re);
        $re = $db->update('t1', ['name'=>'n2'], ['id'=>2]);
        $this->assertEquals(1, $re);
        $re = $db->get('t1', '*', ['id'=>2]);
        $this->assertEquals(['id'=>2, 'name'=>'n2'], $re);
        $re = $db->delete('t1', ['id'=>3]);
        $this->assertEquals(1, $re);
        $re = $db->count('t1');
        $this->assertEquals(2, $re);
        $re = $db->queryAll("select name from t1 where id >= :id", ['id'=>1]);
        $this->assertEquals([['name'=>'name1'], ['name'=>'n2']], $re);
        $re = $db->queryOne("select id from t1 where name = :n", ['n'=>'name1']);
        $this->assertEquals(['id'=>1], $re);
        $re = $db->exec("drop table t1");
        $this->assertEquals(0, $re);
    }
}