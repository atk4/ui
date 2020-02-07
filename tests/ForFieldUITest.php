<?php

namespace atk4\ui\tests;

use atk4\data\Model;
use atk4\data\Persistence;

class MyTestModel extends Model
{
    public function init()
    {
        parent::init();

        $this->addField('regular_field');
        $this->addField('just_for_data', ['never_persist' => true]);
        $this->addField('no_persist_but_show_in_ui', ['never_persist' => true, 'ui' => ['editable' => true]]);
    }
}

/**
 * Test is designed to verify that field which is explicitly editable should appear and be editable
 * even if 'never_persist' is set to true.
 */
class ForFieldUITest extends \atk4\core\PHPUnit_AgileTestCase
{
    /** @var Model */
    public $m;

    public function setUp()
    {
        $a = [];
        $p = new Persistence\Array_($a);
        $this->m = new MyTestModel($p);
    }

    public function testModelLevel()
    {
        $this->assertTrue($this->m->getField('no_persist_but_show_in_ui')->isEditable());
    }

    public function testRegularField()
    {
        $f = new \atk4\ui\Form();
        $f->init();
        $f->setModel($this->m);
        $this->assertFalse($f->getField('regular_field')->readonly);
    }

    public function testJustDataField()
    {
        $f = new \atk4\ui\Form();
        $f->init();
        $f->setModel($this->m, ['just_for_data']);
        $this->assertTrue($f->getField('just_for_data')->readonly);
    }

    public function testShowInUi()
    {
        $f = new \atk4\ui\Form();
        $f->init();
        $f->setModel($this->m);
        $this->assertFalse($f->getField('no_persist_but_show_in_ui')->readonly);
    }
}
