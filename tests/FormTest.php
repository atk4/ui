<?php

namespace atk4\ui\tests;

use atk4\data\Model;
use atk4\ui\App;
use atk4\ui\Form;

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

    public function testFormSubmit()
    {
        $f = new Form();
        $f->app = new AppMock([
            'catch_exceptions'        => false,
            'always_run'              => false,
            'catch_runaway_callbacks' => false,
        ]);

        $f->init();

        $m = new Model();
        $m->addField('name', ['default'=>'John']);
        $m->addField('email', ['required'=>true]);
        $m->addField('is_admin', ['default'=>false]);

        $f->setModel($m, ['name', 'email']);

        $this->assertEquals('John', $f->model->get('name'));

        // fake some POST data
        $_POST = ['atk_submit'=>'ajax', 'email'=>'john@yahoo.com', 'is_admin'=>'1'];

        $submit_called = false;

        $f->onSubmit(function ($f) use (&$submit_called) {

            // field has default, but form didn't send value back
            $this->assertEquals(null, $f->model['name']);

            $this->assertEquals('john@yahoo.com', $f->model['email']);

            // security check, unspecified field must not be changed
            $this->assertEquals(false, $f->model['is_admin']);

            $submit_called = true;
        });

        $f->render();
        unset($f);

        $this->assertTrue($submit_called);
    }
}

class AppMock extends App
{
    public function terminate($output = null)
    {
        // do nothing!
    }
}
