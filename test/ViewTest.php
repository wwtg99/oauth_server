<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/9/21
 * Time: 11:57
 */

class ViewTest extends PHPUnit_Framework_TestCase {
    public static function setUpBeforeClass()
    {
        require '../bootstrap/init.php';
    }

    public function testAssets()
    {
        $am = new \Flight2wwu\Component\View\AssetsManager();
        $am->register();
        $am->boot();
        $test_conf = ['global_pre'=>['css'=>'a1.css', 'js'=>'b1.js'], 'lib1'=>['css'=>['a2.css', 'a3.css']]];
        $am->loadConfig($test_conf);
        echo $am->renderCss();
        echo $am->renderJs();
        $am->addLibrary('lib1');
        echo $am->renderCss();
        echo $am->renderJs();
        $am->addLibrary('lib2');
        echo $am->renderCss();
        echo $am->renderJs();
        $am->addCss('c1.css');
        $am->addJs('j1.js');
        echo $am->renderCss();
        echo $am->renderJs();
        $img = $am->getResource('bubble-sprite.png');
        $this->assertEquals('/asserts/images' . DIRECTORY_SEPARATOR . 'bubble-sprite.png', $img);
    }

}
 