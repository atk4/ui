<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Data\Model;
use Atk4\Data\ValidationException;
use Atk4\Ui\App;
use Atk4\Ui\Callback;
use Atk4\Ui\Exception;
use Atk4\Ui\Exception\UnhandledCallbackExceptionError;
use Atk4\Ui\Form;
use Mvorisek\Atk4\Hintable\Phpstan\PhpstanUtil;

class FormTest extends TestCase
{
    /** @var Form|null */
    public $form;

    /** @var string */
    public $formError;

    protected function setUp(): void
    {
        parent::setUp();

        $this->form = new Form();
        $this->form->setApp(new AppFormTestMock([
            'catchExceptions' => false,
            'alwaysRun' => false,
        ]));
        $this->form->invokeInit();
    }

    public function testGetField(): void
    {
        $f = $this->form;
        $f->addControl('test');

        static::assertInstanceOf(Form\Control::class, $f->getControl('test'));
        static::assertSame($f->getControl('test'), $f->layout->getControl('test'));
    }

    public function assertSubmit(array $postData, \Closure $submitFx = null, \Closure $checkExpectedErrorsFx = null): void
    {
        $wasSubmitCalled = false;
        $_POST = $postData;
        try {
            // trigger callback
            $_GET[Callback::URL_QUERY_TRIGGER_PREFIX . 'atk_submit'] = 'ajax';
            $_GET[Callback::URL_QUERY_TARGET] = 'atk_submit';

            $this->form->onSubmit(function (Form $form) use (&$wasSubmitCalled, $submitFx): void {
                $wasSubmitCalled = true;
                if ($submitFx !== null) {
                    $submitFx($form->model);
                }
            });

            $this->form->render();
            $res = AppFormTestMock::assertInstanceOf($this->form->getApp())->output;

            if ($checkExpectedErrorsFx !== null) {
                static::assertFalse($wasSubmitCalled, 'Expected submission to fail, but it was successful!');
                static::assertNotSame('', $res['atkjs']); // will output useful error
                $this->formError = $res['atkjs'];

                $checkExpectedErrorsFx($res['atkjs']);
            } else {
                static::assertTrue($wasSubmitCalled, 'Expected submission to be successful but it failed');
                static::assertSame('', $res['atkjs']);
            }

            $this->form = null; // we shouldn't submit form twice!
        } finally {
            unset($_GET);
            unset($_POST);
        }
    }

    public function testFormSubmit(): void
    {
        $f = $this->form;

        $m = new Model();
        $m->addField('name', ['default' => 'John']);
        $m->addField('email', ['required' => true]);
        $m->addField('is_admin', ['default' => false]);

        $m = $m->createEntity();
        $f->setModel($m, ['name', 'email']);

        static::assertSame('John', $f->model->get('name'));

        // fake some POST data
        $this->assertSubmit(['email' => 'john@yahoo.com', 'is_admin' => '1'], function (Model $m) {
            // field has default, but form didn't send value back
            static::assertNull($m->get('name'));

            static::assertSame('john@yahoo.com', $m->get('email'));

            // security check, unspecified field must not be changed
            static::assertFalse($m->get('is_admin'));
        });
    }

    public function testTextarea(): void
    {
        $this->form->addControl('Textarea');
        $this->assertSubmit(['Textarea' => '0'], function (Model $m) {
            static::assertSame('0', $m->get('Textarea'));
        });
    }

    public function assertFormControlError(string $field, string $error): void
    {
        $n = preg_match_all('~form\(\'add prompt\', \'([^\']*)\', \'([^\']*)\'\)~', $this->formError, $matchesAll, \PREG_SET_ORDER);
        static::assertGreaterThan(0, $n);
        $matched = false;
        foreach ($matchesAll as $matches) {
            if ($matches[1] === $field) {
                $matched = true;
                static::assertStringContainsString($error, $matches[2], 'Regarding control ' . $field . ' error message');
            }
        }

        static::assertTrue($matched, 'Form control ' . $field . ' did not produce error');
    }

    public function assertFromControlNoErrors(string $field): void
    {
        $n = preg_match_all('~form\(\'add prompt\', \'([^\']*)\', \'([^\']*)\'\)~', $this->formError, $matchesAll, \PREG_SET_ORDER);
        static::assertGreaterThan(0, $n);
        foreach ($matchesAll as $matches) {
            if ($matches[1] === $field) {
                throw new Exception('Form control ' . $field . ' unexpected error: ' . $matches[2]);
            }
        }
    }

    public function testSubmitError(): void
    {
        $m = new Model();

        $options = ['yes please', 'woot'];

        $m->addField('opt1', ['values' => $options]);
        $m->addField('opt2', ['values' => $options]);
        $m->addField('opt3', ['values' => $options, 'nullable' => false]);
        $m->addField('opt3_z', ['values' => $options, 'nullable' => false]);
        $m->addField('opt4', ['values' => $options, 'required' => true]);
        $m->addField('opt4_z', ['values' => $options, 'required' => true]);

        $m = $m->createEntity();
        $this->form->setModel($m);

        $this->assertSubmit(['opt1' => '2', 'opt3_z' => '0', 'opt4' => '', 'opt4_z' => '0'], null, function (string $formError) {
            // dropdown validates to make sure option is proper
            $this->assertFormControlError('opt1', 'not one of the allowed values');

            // user didn't select any option here
            $this->assertFromControlNoErrors('opt2');

            // dropdown insists for value to be there
            $this->assertFormControlError('opt3', 'Must not be null');
            $this->assertFromControlNoErrors('opt3_z');
            $this->assertFormControlError('opt4', 'Must not be empty');
            $this->assertFormControlError('opt4_z', 'Must not be empty');
        });
    }

    public function testSubmitNonFormFieldError(): void
    {
        $m = new Model();
        $m->addField('foo', ['nullable' => false]);
        $m->addField('bar', ['nullable' => false]);

        $m = $m->createEntity();
        $this->form->setModel($m, ['foo']);

        $submitReached = false;
        $catchReached = false;
        try {
            try {
                $this->assertSubmit(['foo' => 'x'], function (Model $model) use (&$submitReached) {
                    $submitReached = true;
                    $model->set('bar', null);
                });
            } catch (UnhandledCallbackExceptionError $e) {
                $catchReached = true;
                static::assertSame('bar', $e->getPrevious()->getParams()['field']->shortName); // @phpstan-ignore-line

                $this->expectException(ValidationException::class);
                $this->expectExceptionMessage('Must not be null');

                throw $e->getPrevious();
            }
        } finally {
            static::assertTrue($submitReached);
            static::assertTrue($catchReached);
        }
    }

    public function testNoDisabledAttrWithHiddenType(): void
    {
        $input = new Form\Control\Input();
        $input->disabled = true;
        $input->readOnly = true;
        static::assertStringContainsString(' disabled="disabled"', $input->render());
        static::assertStringContainsString(' readonly="readonly"', $input->render());

        $input = new Form\Control\Input();
        $input->disabled = true;
        $input->readOnly = true;
        $input->inputType = 'hidden';
        static::assertStringNotContainsString('disabled', $input->render());
        static::assertStringNotContainsString('readonly', $input->render());
    }
}

class AppFormTestMock extends App
{
    /** @var string|array */
    public $output;

    public function terminate($output = '', array $headers = []): void
    {
        $this->output = $output;

        PhpstanUtil::fakeNeverReturn();
    }
}
