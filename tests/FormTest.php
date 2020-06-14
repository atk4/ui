<?php

declare(strict_types=1);

namespace atk4\ui\tests;

use atk4\core\AtkPhpunit;
use atk4\data\Model;
use atk4\ui\App;
use atk4\ui\Form;

class FormTest extends AtkPhpunit\TestCase
{
    /** @var Form */
    public $f;

    /** @var string */
    public $f_error;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->f = new \atk4\ui\Form();
        $this->f->app = new AppMockFT([
            'catch_exceptions' => false,
            'always_run' => false,
            'catch_runaway_callbacks' => false,
        ]);
        $this->f->init();
    }

    /**
     * Some tests for form.
     */
    public function testGetField()
    {
        $f = $this->f;
        $f->addField('test');

        $this->assertTrue($f->getField('test') instanceof \atk4\ui\FormField\Generic);
        $this->assertInstanceOf(\atk4\ui\FormField\Generic::class, $f->layout->getField('test'));
    }

    public function assertSubmit(array $post_data, callable $submit = null, callable $check_expected_error = null)
    {
        $submit_called = false;
        $_POST = $post_data;
        $_POST['atk_submit'] = 'ajax';

        $this->f->onSubmit(function (Form $form) use (&$submit_called, $submit) {
            $submit_called = true;
            if ($submit) {
                call_user_func($submit, $form->model);
            }
        });

        $this->f->render();
        $res = $this->f->app->output;

        if ($check_expected_error) {
            $this->assertFalse($submit_called, 'Expected submission to fail, but it was successful!');
            $this->assertNotSame('', $res['atkjs']); // will output useful error
            $this->f_error = $res['atkjs'];

            call_user_func($check_expected_error, $res['atkjs']);
        } else {
            $this->assertTrue($submit_called, 'Expected submission to be successful but it failed');
            $this->assertSame('', $res['atkjs']); // will output useful error
        }

        $this->f = null;   // we shouldn't submit form twice!

        $_POST = [];
    }

    public function testFormSubmit()
    {
        $f = $this->f;

        $m = new Model();
        $m->addField('name', ['default' => 'John']);
        $m->addField('email', ['required' => true]);
        $m->addField('is_admin', ['default' => false]);

        $f->setModel($m, ['name', 'email']);

        $this->assertSame('John', $f->model->get('name'));

        // fake some POST data
        $this->assertSubmit(['email' => 'john@yahoo.com', 'is_admin' => '1'], function (Model $m) {
            // field has default, but form didn't send value back
            $this->assertNull($m->get('name'));

            $this->assertSame('john@yahoo.com', $m->get('email'));

            // security check, unspecified field must not be changed
            $this->assertFalse($m->get('is_admin'));
        });
    }

    public function testTextArea()
    {
        $this->f->addField('TextArea');
        $this->assertSubmit(['TextArea' => '0'], function (Model $m) {
            $this->assertSame('0', $m->get('TextArea'));
        });
    }

    public function assertSubmitError(array $post, callable $error_callback)
    {
        $this->assertSubmit($post, null, $error_callback);
    }

    public function assertFieldError(string $field, string $error)
    {
        $matched = false;

        preg_replace_callback('/form\("add prompt","([^"]*)","([^"]*)"\)/', function ($matches) use ($error, $field, &$matched) {
            if ($matches[1] === $field) {
                $this->assertStringContainsString($error, $matches[2], 'Regarding field ' . $field . ' error message');

                $matched = true;
            }
        }, $this->f_error);

        $this->assertTrue($matched, 'Field ' . $field . ' did not produce error');
    }

    public function assertFieldNoErrors(string $field)
    {
        preg_replace_callback('/form\("add prompt","([^"]*)","([^"]*)"\)/', function ($matches) use ($field, &$matched) {
            if ($matches[1] === $field) {
                $this->fail('Field ' . $field . ' unexpected error: ' . $matches[2]);
            }
        }, $this->f_error);
    }

    public function testSubmitError()
    {
        $m = new Model();

        $options = ['0' => 'yes please', '1' => 'woot'];

        $m->addField('opt1', ['values' => $options]);
        $m->addField('opt2', ['values' => $options]);
        $m->addField('opt3', ['values' => $options, 'required' => true]);
        //$m->addField('opt3_zerotest', ['values'=>$options, 'required'=>true]);
        $m->addField('opt4', ['values' => $options, 'mandatory' => true]);

        $this->f->setModel($m);
        $this->assertSubmitError(['opt1' => '2', 'opt3' => '', 'opt3_zerotest' => '0'], function ($error) {
            // dropdown validates to make sure option is proper
            $this->assertFieldError('opt1', 'not one of the allowed values');

            // user didn't select any option here
            $this->assertFieldNoErrors('opt2');

            // dropdown insists for value to be there
            $this->assertFieldError('opt3', 'Must not be empty');

            // value with '0' is valid selection
            // TODO: currently fails!! See https://github.com/atk4/ui/issues/781
            //$this->assertFieldNoErrors('opt3_zerotest');

            // mandatory will error during save(), but form does not care about it. This is normal
            // as there may be further changes to this field on beforeSave hook...
            $this->assertFieldNoErrors('opt4');
        });
    }
}

class AppMockFT extends App
{
    public $output;

    public function terminate($output = '', array $headers = []): void
    {
        $this->output = $output;
        // do nothing!
    }
}
