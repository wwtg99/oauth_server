<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/2/19
 * Time: 11:50
 */

class MedooTest extends PHPUnit_Framework_TestCase {

    public static function setUpBeforeClass()
    {
        require '../bootstrap/init.php';
    }

    public function testQuery()
    {
        $conf = ['driver'=>'pgsql', 'host'=>'192.168.0.31', 'port'=>32768, 'dbname'=>'t2', 'user'=>'lims_gw', 'password'=>'1'];
        $db = new \Flight2wwu\Component\Database\MedooDB();
        $db->connect($conf);
        $re = $db->queryOne("select * from users where id = 'U0000001'");
        $this->assertEquals('admin', $re['name']);
        var_dump($re);
    }
}
 