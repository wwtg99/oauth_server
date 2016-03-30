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
        $this->assertEquals('/resource' . DIRECTORY_SEPARATOR . 'bubble-sprite.png', $img);
    }

    public function testHtmlTag()
    {
        $tag1 = new \Flight2wwu\Component\View\Html\HtmlTag('div');
        $exp1 = "<div></div>";
        $this->assertEquals($exp1, $tag1->render());
        $tag2 = new \Flight2wwu\Component\View\Html\HtmlTag('p', 'text', ['id'=>'id1']);
        $exp2 = "<p id='id1'>text</p>";
        $this->assertEquals($exp2, $tag2->render());
        $tag3 = new \Flight2wwu\Component\View\Html\HtmlTag('br', '', [], true);
        $exp3 = "<br />";
        $this->assertEquals($exp3, $tag3->render());
        $tag2->addClass('c1');
        $exp4 = "<p id='id1' class='c1'>text</p>";
        $this->assertEquals($exp4, $tag2->render());
        $tag2->addClass('c2');
        $tag2->addAttr('name', 'name1');
        $tag2->addAttr('id', 'id2');
        $exp5 = "<p id='id2' class='c1 c2' name='name1'>text</p>";
        $this->assertEquals($exp5, $tag2->render());
        $tag2->removeClass('c1');
        $tag2->addAttr('id', null);
        $tag2->addAttr('name', null);
        $tag1->addChild($tag2);
        $exp6 = "<div><p class='c2'>text</p></div>";
        $this->assertEquals($exp6, $tag1->render());
    }
}
 