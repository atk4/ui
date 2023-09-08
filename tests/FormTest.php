<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Data\Model;
use Atk4\Data\Model\EntityFieldPair;
use Atk4\Data\ValidationException;
use Atk4\Ui\App;
use Atk4\Ui\Callback;
use Atk4\Ui\Exception;
use Atk4\Ui\Exception\UnhandledCallbackExceptionError;
use Atk4\Ui\Form;
use Mvorisek\Atk4\Hintable\Phpstan\PhpstanUtil;

class FormTest extends TestCase
{
    use CreateAppTrait;

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

    public function testAddControlAlreadyExistsException(): void
    {
        $t = new Form();
        $t->setApp($this->createApp());
        $t->invokeInit();
        $t->addControl('foo');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Form field already exists');
        $t->addControl('foo');
    }

    /**
     * @param \Closure(Model): void  $submitFx
     * @param \Closure(string): void $checkExpectedErrorsFx
     */
    public function assertFormSubmit(array $postData, \Closure $submitFx = null, \Closure $checkExpectedErrorsFx = null): void
    {
        $wasSubmitCalled = false;
        try {
            // trigger callback

            $this->replaceAppRequestGet($this->form->getApp(), array_merge(
                $this->form->getApp()->getRequest()->getQueryParams(),
                [
                    Callback::URL_QUERY_TRIGGER_PREFIX . 'atk_submit' => 'ajax',
                    Callback::URL_QUERY_TARGET => 'atk_submit',
                ]
            ));

            $this->replaceAppRequestPost(
                $this->form->getApp(),
                array_merge(array_map(static fn () => '', $this->form->controls), $postData)
            );

            $this->form->onSubmit(static function (Form $form) use (&$wasSubmitCalled, $submitFx): void {
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
                static::assertNull($res['atkjs']);
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
        $this->assertFormSubmit(['email' => 'john@yahoo.com', 'is_admin' => '1'], static function (Model $m) {
            // field has default, but form send back empty value
            self::assertSame('', $m->get('name'));

            self::assertSame('john@yahoo.com', $m->get('email'));

            // security check, unspecified field must not be changed
            self::assertFalse($m->get('is_admin'));
        });
    }

    public function testTextareaSubmit(): void
    {
        $this->form->addControl('Textarea');
        $this->assertFormSubmit(['Textarea' => '0'], static function (Model $m) {
            self::assertSame('0', $m->get('Textarea'));
        });
    }

    public function assertFormControlError(string $field, string $error): void
    {
        $n = preg_match_all('~\.form\(\'add prompt\', \'([^\']*)\', \'([^\']*)\'\)~', $this->formError, $matchesAll, \PREG_SET_ORDER);
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

    public function assertFormControlNoErrors(string $field): void
    {
        $n = preg_match_all('~\.form\(\'add prompt\', \'([^\']*)\', \'([^\']*)\'\)~', $this->formError, $matchesAll, \PREG_SET_ORDER);
        static::assertGreaterThan(0, $n);
        foreach ($matchesAll as $matches) {
            if ($matches[1] === $field) {
                throw new Exception('Form control ' . $field . ' unexpected error: ' . $matches[2]);
            }
        }
    }

    public function testFormSubmitError(): void
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

        $this->assertFormSubmit(['opt1' => '2', 'opt3_z' => '0', 'opt4' => '', 'opt4_z' => '0'], null, function (string $formError) {
            // dropdown validates to make sure option is proper
            $this->assertFormControlError('opt1', 'not one of the allowed values');

            // user didn't select any option here
            $this->assertFormControlNoErrors('opt2');

            // dropdown insists for value to be there
            $this->assertFormControlNoErrors('opt3');
            $this->assertFormControlNoErrors('opt3_z');
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
                $this->assertFormSubmit(['foo' => 'x'], static function (Model $model) use (&$submitReached) {
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
        $input = new Form\Control\Line();
        $input->readOnly = true;
        $input->setApp($this->createApp());
        static::assertStringContainsString(' readonly="readonly"', $input->render());
        static::assertStringNotContainsString('disabled', $input->render());

        $input = new Form\Control\Line();
        $input->disabled = true;
        $input->readOnly = true;
        $input->setApp($this->createApp());
        static::assertStringContainsString(' disabled="disabled"', $input->render());
        static::assertStringNotContainsString('readonly', $input->render());

        $input = new Form\Control\Hidden();
        $input->disabled = true;
        $input->readOnly = true;
        $input->setApp($this->createApp());
        static::assertStringNotContainsString('disabled', $input->render());
        static::assertStringNotContainsString('readonly', $input->render());
    }

    public function testCheckboxWithNonBooleanException(): void
    {
        $input = new Form\Control\Checkbox();
        $input->setApp($this->createApp());
        $input->invokeInit();

        $m = new Model();
        $m->addField('foo');
        $input->entityField = new EntityFieldPair($m->createEntity(), 'foo');
        $input->entityField->set('1');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Checkbox form control requires field with boolean type');
        $input->render();
    }

    public function testUploadNoUploadCallbackException(): void
    {
        $input = new Form\Control\Upload();
        $input->setApp($this->createApp());
        $input->invokeInit();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Missing onUpload callback');
        try {
            $this->replaceAppRequestGet($input->getApp(), [Callback::URL_QUERY_TARGET => $input->cb->getUrlTrigger()]);
            $this->replaceAppRequestPost($input->getApp(), ['fUploadAction' => Form\Control\Upload::UPLOAD_ACTION]);

            $input->render();
        } finally {
            unset($_GET);
            unset($_POST);
        }
    }

    public function testUploadNoDeleteCallbackException(): void
    {
        $input = new Form\Control\Upload();
        $input->setApp($this->createApp());
        $input->invokeInit();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Missing onDelete callback');
        try {
            $this->replaceAppRequestGet($input->getApp(), [Callback::URL_QUERY_TARGET => $input->cb->getUrlTrigger()]);
            $this->replaceAppRequestPost($input->getApp(), ['fUploadAction' => Form\Control\Upload::DELETE_ACTION]);
            $input->render();
        } finally {
            unset($_GET);
            unset($_POST);
        }
    }
}

class AppFormTestMock extends App
{
    /** @var string|array */
    public $output;

    public function terminate($output = ''): void
    {
        $this->output = $output;

        PhpstanUtil::fakeNeverReturn();
    }
}
