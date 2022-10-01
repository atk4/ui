<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Data\Model;
use Atk4\Data\Persistence;
use Atk4\Ui\Form;

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
        parent::setUp();

        $p = new Persistence\Array_();
        $this->m = new MyTestModel($p);
    }

    public function testModelLevel(): void
    {
        static::assertTrue($this->m->getField('no_persist_but_show_in_ui')->isEditable());
    }

    public function testRegularField(): void
    {
        $f = new Form();
        $f->invokeInit();
        $f->setModel($this->m->createEntity());
        static::assertFalse($f->getControl('regular_field')->readOnly);
    }

    public function testJustDataField(): void
    {
        $f = new Form();
        $f->invokeInit();
        $f->setModel($this->m->createEntity(), ['just_for_data']);
        static::assertTrue($f->getControl('just_for_data')->readOnly);
    }

    public function testShowInUi(): void
    {
        $f = new Form();
        $f->invokeInit();
        $f->setModel($this->m->createEntity());
        static::assertFalse($f->getControl('no_persist_but_show_in_ui')->readOnly);
    }
}
