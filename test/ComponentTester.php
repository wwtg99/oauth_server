<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/9/8
 * Time: 15:54
 */

class ComponentTester extends PHPUnit_Framework_TestCase
{

    public static function setUpBeforeClass()
    {
        require '../bootstrap/init.php';
    }

    public function testTrans()
    {
        $trans = new \Flight2wwu\Component\Translation\SymTrans();
        $trans->boot();
        $trans->addResource('zh_CN', 'messages');
        $re = $trans->trans('page not found');
        $this->assertEquals('您访问的页面不存在！', $re);
        $re = $trans->trans('not login', [], true);
        $this->assertEquals('您尚未登录！', $re);
        $re = $trans->trans('Not Login', [], true);
        $this->assertEquals('您尚未登录！', $re);
        $data = ['a'=>'page not found', 'b'=>['c'=>'not login', 'd'=>'login failed']];
        $re = $trans->transArray($data);
        $this->assertEquals('登录失败！', $re['b']['d']);
        $trans->setLocale('en_AM');
        $re = $trans->trans('login failed');
        $this->assertEquals('Fail to login!', $re);
    }

    public function testCache()
    {
        $cache = new \Flight2wwu\Component\Storage\Cache();
        $cache->register();
        $cache->boot();
        $data = ['aa'=>'aaa', 'k1'=>'ggg', 'k2'=>['ga', 'ha']];
        foreach ($data as $k => $v) {
            $cache->getCache()->set($k, $v);
            $this->assertTrue($cache->getCache()->has($k));
            $gv = $cache->getCache()->get($k);
            $this->assertEquals($v, $gv);
        }
    }

    public function testRbac()
    {
        $rbac = new \Flight2wwu\Component\Auth\RoleAuth();
        $rbac->loadConfig([
            'rbac'=>[
                'admin'=>['*'=>3],
                'common_user'=>[
                    '*'=>0,
                    '/view/*'=>3,
                    '/tran'=>1
                ],
                'test1'=>[
                    '/tran'=>2,
                    '/view/test/*'=>2
                ]
            ]
        ]);
        $a = $rbac->getRoleAuth('admin', '*');
        $this->assertEquals(3, $a);
        $a = $rbac->getRoleAuth('common_user', '*');
        $this->assertEquals(0, $a);
        $a = $rbac->getRoleAuth('common_user', '/tran');
        $this->assertEquals(1, $a);
        $a = $rbac->getPathAuth('common_user', '/view/a');
        $this->assertEquals(3, $a);
        $a = $rbac->getPathAuth('common_user', '/vie');
        $this->assertEquals(0, $a);
        $a = $rbac->getRoleAuth('admin', '/v');
        $this->assertEquals(-1, $a);
        $a = $rbac->getRoleAuth('common_user', '/v');
        $this->assertEquals(-1, $a);
        $a = $rbac->getRoleAuth(['admin', 'common_user'], '/v');
        $this->assertEquals(-1, $a);
        $a = $rbac->getRoleAuth('admin', '*');
        $this->assertEquals(3, $a);
        $a = $rbac->getRoleAuth('common_user', '*');
        $this->assertEquals(0, $a);
        $a = $rbac->getRoleAuth(['admin', 'common_user'], '*');
        $this->assertEquals(3, $a);
        $a = $rbac->getPathAuth(['admin', 'common_user'], '/v');
        $this->assertEquals(3, $a);
        $a = $rbac->getPathAuth(['test1', 'common_user'], '/tran');
        $this->assertEquals(3, $a);
        $a = $rbac->getPathAuth(['test1', 'common_user'], '/view/test/a');
        $this->assertEquals(2, $a);
        $a = $rbac->getPathAuth('common_user', '/view/test/a');
        $this->assertEquals(3, $a);
    }

    public function testMail()
    {
        $mail = new \Flight2wwu\Component\Utils\Mail();
        $re = $mail->send([
            'subject'=>'test',
            'from'=>['test@flight2wwu.com'],
            'to'=>['wwu@genowise.com'],
            'body'=>'test mail'
        ]);
        $this->assertEquals(0, $re);
    }

    public function testExpress()
    {
        $test = [
            '1063163730100'=>'ems',
            '114750081239'=>'sf',
            '568657570837'=>'sto',
            '1200878623037'=>'yd',
            '199130201363'=>'sf'
        ];
        $exp = new \Flight2wwu\Component\Utils\Express();
        $config = [
            ['yto', '圆通', 'http://www.kiees.cn/yto.php?wen=<no>&action=ajax', 'extractKieesTable'],
            ['sto', '申通', 'http://www.kiees.cn/sto.php?wen=<no>&ajax=1', 'extractKieesTable'],
            ['ems', 'EMS', 'http://www.kiees.cn/ems.php?wen=<no>&action=ajax', 'extractKieesTable'],
            ['sf', '顺风', 'http://www.kiees.cn/sf.php?wen=<no>&action=ajax', 'extractKieesTable'],
            ['zto', '中通', 'http://www.kiees.cn/zto.php?wen=<no>&action=ajax', 'extractKieesTable'],
            ['yd', '韵达', 'http://www.kiees.cn/yd.php?wen=<no>&channel', 'extractKieesTable'],
        ];
        $exp->loadConfig($config);
        foreach ($test as $no => $com) {
            $arr = $exp->track($com, $no);
            $last = $exp->current($com, $no);
            if ($arr) {
                $this->assertEquals($last, $arr[count($arr) - 1]);
            }
            var_dump($arr);
        }

    }

}
 