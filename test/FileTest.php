<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/9/22
 * Time: 9:53
 */

class FileTest extends PHPUnit_Framework_TestCase {
    public static function setUpBeforeClass()
    {
        require '../bootstrap/init.php';
    }

    public function testSection() {
        $name = 'name1';
        $data = [['f1'=>'v1', 'f2'=>'v2'], ['f1'=>'v3', 'f2'=>'v4']];
        $head = [['title'=>'t1', 'field'=>'f1', 'type'=>'string'], ['title'=>'t2', 'field'=>'f2', 'type'=>'int']];
        $s = new \Flight2wwu\Component\File\Sections\Section(
            \Flight2wwu\Component\File\Sections\Section::KV_SECTION, $name, $data, $head, []
        );
        $this->assertEquals($s->getDel(), "\t");
        $this->assertEquals($s->getName(), $name);
        $this->assertEquals($s->getType(), \Flight2wwu\Component\File\Sections\Section::KV_SECTION);
        $this->assertEquals($s->getNull(), '-');
        $this->assertEquals($s->getPostfix(), '');
        $this->assertEquals($s->getPrefix(), '');
        $this->assertTrue($s->getShowName());
        $this->assertTrue($s->getShowHead());
        $this->assertEquals($s->getSkip(), []);
        $this->assertEquals($s->getHead(), $head);
        $this->assertEquals($s->getData(), $data);
        $rule = ['null'=>'*', 'skip'=>['f1'], 'del'=>'|', 'prefix'=>'#', 'postfix'=>'$', 'showName'=>false, 'showHead'=>false];
        $s = new \Flight2wwu\Component\File\Sections\Section(
            \Flight2wwu\Component\File\Sections\Section::KV_SECTION, $name, $data, $head, $rule
        );
        $this->assertEquals($s->getDel(), "|");
        $this->assertEquals($s->getName(), $name);
        $this->assertEquals($s->getType(), \Flight2wwu\Component\File\Sections\Section::KV_SECTION);
        $this->assertEquals($s->getNull(), '*');
        $this->assertEquals($s->getPostfix(), '$');
        $this->assertEquals($s->getPrefix(), '#');
        $this->assertFalse($s->getShowName());
        $this->assertFalse($s->getShowHead());
        $this->assertEquals($s->getSkip(), ['f1']);
        $this->assertEquals($s->getHead(), $head);
        $this->assertEquals($s->getData(), $data);
    }

    public function testSectionFile() {
        $name1 = 'name1';
        $data1 = ['f1'=>'v1', 'f2'=>'v2'];
        $head1 = [];
        $name2 = 'name2';
        $data2 = [['f1'=>'v1', 'f2'=>'v2'], ['f1'=>'v3', 'f2'=>'v4']];
        $head2 = [['title'=>'t1', 'field'=>'f1', 'type'=>'string'], ['title'=>'t2', 'field'=>'f2', 'type'=>'int']];
        $rule = ['null'=>'*', 'skip'=>['f1'], 'del'=>'|', 'prefix'=>'#', 'postfix'=>'$', 'showName'=>false, 'showHead'=>false];
        $s1 = new \Flight2wwu\Component\File\Sections\Section(
            \Flight2wwu\Component\File\Sections\Section::KV_SECTION, $name1, $data1, $head1, $rule
        );
        $s2 = new \Flight2wwu\Component\File\Sections\Section(
            \Flight2wwu\Component\File\Sections\Section::TSV_SECTION, $name2, $data2, $head2, $rule
        );
        $sf1 = new \Flight2wwu\Component\File\Sections\SectionFile([$s1, $s2]);
        $sf2 = new \Flight2wwu\Component\File\Sections\SectionFile();
        $sf2->addSections([$s1, $s2]);
        $sf3 = new \Flight2wwu\Component\File\Sections\SectionFile();
        $sf3->addSection($s1)->addSection($s2);
        $this->assertEquals($sf1, $sf2);
        $this->assertEquals($sf2, $sf3);
        $this->assertEquals(2, $sf2->getCount());
        $this->assertEquals(1, $sf1->getIndexByName($name2));
        $this->assertEquals(-1, $sf1->getIndexByName('no'));
        $this->assertEquals($s2, $sf1->getSectionByName($name2));
        $this->assertEquals(null, $sf1->getSectionByName('no'));
    }

    public function testTxtFile() {
        $name1 = 'name1';
        $data1 = ['f1'=>'v1', 'f2'=>'v2'];
        $head1 = [];
        $name2 = 'name2';
        $data2 = [['f1'=>'v1', 'f2'=>'v2'], ['f1'=>'v3', 'f2'=>'v4']];
        $head2 = [['title'=>'t1', 'field'=>'f1', 'type'=>'string'], ['title'=>'t2', 'field'=>'f2', 'type'=>'int']];
        $rule = [];
        $s1 = new \Flight2wwu\Component\File\Sections\Section(
            \Flight2wwu\Component\File\Sections\Section::KV_SECTION, $name1, $data1, $head1, $rule
        );
        $s2 = new \Flight2wwu\Component\File\Sections\Section(
            \Flight2wwu\Component\File\Sections\Section::TSV_SECTION, $name2, $data2, $head2, $rule
        );
        $sf1 = new \Flight2wwu\Component\File\Sections\SectionFile([$s1, $s2]);
        $txt1 = \Flight2wwu\Component\File\TxtFile::createFromSection($sf1);
        $expfile1 = "[name1]\nf1\tv1\nf2\tv2\n\n[name2]\nt1\tt2\nv1\tv2\nv3\tv4\n";
        $txt2 = \Flight2wwu\Component\File\TxtFile::createFromStr($expfile1);
        $this->assertEquals($txt2->getContent(), $txt1->getContent());

        $data1 = ['f1'=>'v1', 'f2'=>'v2'];
        $head1 = [['title'=>'t1', 'field'=>'f1'], ['title'=>'f2', 'field'=>'f2']];

        $data2 = [['f1'=>'v1', 'f2'=>'v2'], ['f1'=>null, 'f2'=>'v4']];
        $head2 = [['title'=>'t1', 'field'=>'f1', 'type'=>'string'], ['title'=>'t2', 'field'=>'f2', 'type'=>'int']];
        $rule = ['null'=>'*', 'skip'=>['f2'], 'del'=>'|', 'prefix'=>'#', 'postfix'=>'$', 'showName'=>true, 'showHead'=>true];
        $s1 = new \Flight2wwu\Component\File\Sections\Section(
            \Flight2wwu\Component\File\Sections\Section::KV_SECTION, '', $data1, $head1, $rule
        );
        $s2 = new \Flight2wwu\Component\File\Sections\Section(
            \Flight2wwu\Component\File\Sections\Section::TSV_SECTION, '', $data2, $head2, $rule
        );
        $sf1 = new \Flight2wwu\Component\File\Sections\SectionFile([$s1, $s2]);
        $txt1 = \Flight2wwu\Component\File\TxtFile::createFromSection($sf1);
        $expfile1 = "[Section 1]\n#t1|v1$\n\n[Section 2]\nt1\n#v1$\n#*$\n";
        $txt2 = \Flight2wwu\Component\File\TxtFile::createFromStr($expfile1);
        $this->assertEquals($txt2->getContent(), $txt1->getContent());
    }

    public function testExcelFile() {
        $data1 = ['f1'=>'v1', 'f2'=>'v2'];
        $head1 = [['title'=>'t1', 'field'=>'f1'], ['title'=>'f2', 'field'=>'f2']];

        $data2 = [['f1'=>'v1', 'f2'=>'v2'], ['f1'=>null, 'f2'=>'v4']];
        $head2 = [['title'=>'t1', 'field'=>'f1', 'type'=>'string'], ['title'=>'t2', 'field'=>'f2', 'type'=>'int']];
        $rule = ['null'=>'*', 'skip'=>['f2'], 'del'=>'|', 'prefix'=>'#', 'postfix'=>'$', 'showName'=>true, 'showHead'=>true];
        $s1 = new \Flight2wwu\Component\File\Sections\Section(
            \Flight2wwu\Component\File\Sections\Section::KV_SECTION, '', $data1, $head1, $rule
        );
        $s2 = new \Flight2wwu\Component\File\Sections\Section(
            \Flight2wwu\Component\File\Sections\Section::TSV_SECTION, '', $data2, $head2, $rule
        );
        $sf1 = new \Flight2wwu\Component\File\Sections\SectionFile([$s1, $s2]);
        $excel1 = \Flight2wwu\Component\File\ExcelFile::createFromSection($sf1);
        $excel2 = \Flight2wwu\Component\File\ExcelFile::createFromFile(ROOT . '/test/files/download.xlsx');
        $excel1->writeTo(ROOT . '/test/files/out.xlsx');
    }

    public function testReadTsv() {
        $qcfile = ROOT . '/test/files/qc.tsv';
        $sec = \Flight2wwu\Component\File\Sections\SectionFile::fromTxtFile($qcfile, ['Header'=>['type'=>'kv'], 'Plate QC'=>['type'=>'tsv'], 'Sample QC'=>['type'=>'tsv']]);
        $txt = \Flight2wwu\Component\File\TxtFile::createFromSection($sec);
        $exp = file_get_contents($qcfile);
        $this->assertEquals($exp, $txt->getContent());
    }
}
 