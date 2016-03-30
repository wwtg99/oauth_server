<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/12/9
 * Time: 16:39
 */

class PdoDBTest extends PHPUnit_Framework_TestCase
{

    public static function setUpBeforeClass()
    {
        require '../bootstrap/init.php';
    }

    public function testQuery()
    {
        $dbconfig = ['driver'=>'pgsql', 'host'=>'192.168.0.31', 'port'=>5432, 'dbname'=>'test2', 'user'=>'lims_gw', 'password'=>'1'];
        $db = new \Flight2wwu\Component\Database\PdoDB();
        $db->connect($dbconfig);
        $re = $db->query("select * from workflow where id = 'WFSZ150723110023'");
        $this->assertEquals('WFSZ150723110023', $re[0]['id']);
        $re = $db->query("select * from workflow where id = :id", ['id'=>'WFSZ150723110023']);
        $this->assertEquals('WFSZ150723110023', $re[0]['id']);
        $re = $db->query("select * from workflow where id = ?", [1=>'WFSZ150723110023']);
        $this->assertEquals('WFSZ150723110023', $re[0]['id']);
        $re = $db->exec("insert into num_cache (name, val) values (:name, :val)", ['name'=>'t1', 'val'=>1]);
        $this->assertEquals(1, $re);
        $re = $db->queryOne("select * from num_cache where name = :name", ['name'=>'t1']);
        $this->assertEquals(1, $re['val']);
        $re = $db->exec("delete from num_cache where name = :name", ['name'=>'t1']);
        $this->assertEquals(1, $re);
        $lob = file_get_contents('files/logo.png');
        $re = $db->exec("insert into accessories (id, label, contents) values (:id, :lab, :cont)", ['id'=>'acc1', 'lab'=>'label', 'cont'=>[$lob, PDO::PARAM_LOB]]);
        $this->assertEquals(1, $re);
        $re = $db->queryOne("select contents from accessories where id = :id", ['id'=>'acc1']);
        file_put_contents('files/logo_test.png', $re['contents']);
        $re = $db->exec("delete from accessories where id = :id", ['id'=>'acc1']);
        $this->assertEquals(1, $re);
    }

    function testFormat()
    {
        $dbconfig = ['driver'=>'pgsql', 'host'=>'192.168.0.31', 'port'=>5432, 'dbname'=>'test2', 'user'=>'lims_gw', 'password'=>'1'];
        $db = new \Flight2wwu\Component\Database\PdoDB();
        $db->connect($dbconfig);
        $tests_par = [
            "[f1.eq.aa]"=>"f1 = 'aa'",
            "[f2#.gt.1]"=>"f2 > 1",
            "[f1.ne.bb] and [f2.eq.cc]"=>"f1 != 'bb' and f2 = 'cc'",
            "([f1.eq.aa] and [f2.like.%bb%]) or ([f3.ge.2015-1-1] and [f4.le.2016-1-1])"=>"(f1 = 'aa' and f2 like '%bb%') or (f3 >= '2015-1-1' and f4 <= '2016-1-1')",
            "[f1.nee.aa] and [f2 ne.aa]"=>" and "
        ];
        foreach ($tests_par as $t => $exp) {
            $wh = $db->formatWhereParams($t);
            $this->assertEquals($exp, $wh);
        }
        $tests_arr = [
            "f1 = 'aa'"=>[['f1', '=', 'aa']],
            "f1 = 'bb' and f2 > 1"=>[['f1', '=', 'bb'], ['f2', '>', 1]],
            "f1 > 1 and f1 <= 2 and f2 like '%aa%'"=>[['f1', '>', '1', 'integer'], ['f1', '<=', 2], ['f2', 'like', '%aa%']],
            "f1 is null and f2 ~* 'aa'"=>[['f1', 'is', null], ['f3', '=', null], ['f2', '~*', 'aa']]
        ];
        foreach ($tests_arr as $exp => $t) {
            $wh = $db->formatWhereArray($t);
            $this->assertEquals($exp, $wh);
        }

    }

}
 