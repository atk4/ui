<?php

namespace atk4\ui\tests;

class FormTest extends \atk4\core\PHPUnit_AgileTestCase
{
    /**
     * Some tests for form.
     */
    public function testGetField()
    {
        $f = new \atk4\ui\Form();
        $f->init();

        $f->addField('test');

        $this->assertTrue($f->getField('test') instanceof \atk4\ui\FormField\Generic);
        $this->assertInstanceOf(\atk4\ui\FormField\Generic::class, $f->layout->getField('test'));
    }
}
