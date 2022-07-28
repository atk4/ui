<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Data\Model;
use Atk4\Data\Persistence;

class MyTestModel extends Model
{
    protected function init(): void
    {
        parent::init();

        $this->addField('regular_field');
        $this->addField('just_for_data', ['neverPersist' => true]);
        $this->addField('no_persist_but_show_in_ui', ['neverPersist' => true, 'ui' => ['editable' => true]]);
    }
}

/**
 * Test is designed to verify that field which is explicitly editable should appear and be editable
 * even if 'neverPersist' is set to true.
 */
class ForFieldUiTest extends TestCase
{
    /** @var Model */
    public $m;

    protected function setUp(): void
    {
        $p = new Persistence\Array_();
        $this->m = new MyTestModel($p);
    }

    public function testModelLevel(): void
    {
        $this->assertTrue($this->m->getField('no_persist_but_show_in_ui')->isEditable());
    }

    public function testRegularField(): void
    {
        $f = new \Atk4\Ui\Form();
        $f->invokeInit();
        $f->setModel($this->m->createEntity());
        $this->assertFalse($f->getControl('regular_field')->readonly);
    }

    public function testJustDataField(): void
    {
        $f = new \Atk4\Ui\Form();
        $f->invokeInit();
        $f->setModel($this->m->createEntity(), ['just_for_data']);
        $this->assertTrue($f->getControl('just_for_data')->readonly);
    }

    public function testShowInUi(): void
    {
        $f = new \Atk4\Ui\Form();
        $f->invokeInit();
        $f->setModel($this->m->createEntity());
        $this->assertFalse($f->getControl('no_persist_but_show_in_ui')->readonly);
    }
}
