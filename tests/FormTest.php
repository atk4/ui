<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Data\Model;
use Atk4\Data\Model\EntityFieldPair;
use Atk4\Data\ValidationException;
use Atk4\Ui\App;
use Atk4\Ui\Exception;
use Atk4\Ui\Exception\UnhandledCallbackExceptionError;
use Atk4\Ui\Form;
use Mvorisek\Atk4\Hintable\Phpstan\PhpstanUtil;
use Psr\Http\Message\ServerRequestInterface;

class FormTest extends TestCase
{
    use CreateAppTrait;

    /** @var string */
    protected $formError;

    public function testGetField(): void
    {
        $form = new Form();
        $form->setApp($this->createApp());
        $form->invokeInit();

        $form->addControl('test');

        self::assertInstanceOf(Form\Control::class, $form->getControl('test'));
        self::assertSame($form->getControl('test'), $form->layout->getControl('test'));
    }

    public function testAddControlAlreadyExistsException(): void
    {
        $form = new Form();
        $form->setApp($this->createApp());
        $form->invokeInit();
        $form->addControl('foo');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Form field already exists');
        $form->addControl('foo');
    }

    protected function triggerFormSubmit(ServerRequestInterface $request, Form $form, array $postData): ServerRequestInterface
    {
        $request = $this->triggerCallback($request, $form->cb);

        $request = $request->withMethod('POST');
        $request = $request->withParsedBody(array_merge(
            $request->getParsedBody() ?? [],
            array_merge(array_map(static fn () => '', $form->controls), $postData),
        ));

        return $request;
    }

    /**
     * @param \Closure(App): Form    $createFormFx
     * @param \Closure(Model): void  $submitFx
     * @param \Closure(string): void $checkExpectedErrorsFx
     */
    protected function assertFormSubmit(\Closure $createFormFx, array $postData, \Closure $submitFx = null, \Closure $checkExpectedErrorsFx = null): void
    {
        $form = $this->simulateViewCallback(function (ServerRequestInterface $request) use ($createFormFx) {
            $app = $this->createApp([AppFormTestMock::class, 'request' => $request]);
            $form = $createFormFx($app);

            return $form;
        }, function (Form $form) use ($postData) {
            $request = $this->triggerFormSubmit($form->getApp()->getRequest(), $form, $postData);

            return $request;
        });

        $wasSubmitCalled = false;
        $form->onSubmit(static function (Form $form) use (&$wasSubmitCalled, $submitFx): void {
            $wasSubmitCalled = true;
            if ($submitFx !== null) {
                $submitFx($form->model);
            }
        });

        $form->render();
        $res = AppFormTestMock::assertInstanceOf($form->getApp())->output;

        if ($checkExpectedErrorsFx !== null) {
            self::assertFalse($wasSubmitCalled, 'Expected submission to fail, but it was successful!');
            self::assertNotSame('', $res['atkjs']); // will output useful error
            $this->formError = $res['atkjs'];

            $checkExpectedErrorsFx($res['atkjs']);
        } else {
            self::assertTrue($wasSubmitCalled, 'Expected submission to be successful but it failed');
            self::assertNull($res['atkjs']);
        }
    }

    public function testFormSubmit(): void
    {
        // fake some POST data
        $this->assertFormSubmit(static function (App $app) {
            $form = Form::addTo($app);

            $m = new Model();
            $m->addField('name', ['default' => 'John']);
            $m->addField('email', ['required' => true]);
            $m->addField('is_admin', ['default' => false]);

            $form->setModel($m->createEntity(), ['name', 'email']);

            self::assertSame('John', $form->model->get('name'));

            return $form;
        }, ['email' => 'john@yahoo.com', 'is_admin' => '1'], static function (Model $m) {
            // field has default, but form send back empty value
            self::assertSame('', $m->get('name'));

            self::assertSame('john@yahoo.com', $m->get('email'));

            // security check, unspecified field must not be changed
            self::assertFalse($m->get('is_admin'));
        });
    }

    public function testTextareaSubmit(): void
    {
        $this->assertFormSubmit(static function (App $app) {
            $form = Form::addTo($app);
            $form->addControl('Textarea');

            return $form;
        }, ['Textarea' => '0'], static function (Model $m) {
            self::assertSame('0', $m->get('Textarea'));
        });
    }

    protected function assertFormControlError(string $field, string $error): void
    {
        $n = preg_match_all('~\.form\(\'add prompt\', \'([^\']*)\', \'([^\']*)\'\)~', $this->formError, $matchesAll, \PREG_SET_ORDER);
        self::assertGreaterThan(0, $n);
        $matched = false;
        foreach ($matchesAll as $matches) {
            if ($matches[1] === $field) {
                $matched = true;
                self::assertStringContainsString($error, $matches[2], 'Regarding control ' . $field . ' error message');
            }
        }

        self::assertTrue($matched, 'Form control ' . $field . ' did not produce error');
    }

    protected function assertFormControlNoErrors(string $field): void
    {
        $n = preg_match_all('~\.form\(\'add prompt\', \'([^\']*)\', \'([^\']*)\'\)~', $this->formError, $matchesAll, \PREG_SET_ORDER);
        self::assertGreaterThan(0, $n);
        foreach ($matchesAll as $matches) {
            if ($matches[1] === $field) {
                throw new Exception('Form control ' . $field . ' unexpected error: ' . $matches[2]);
            }
        }
    }

    public function testFormSubmitError(): void
    {
        $this->assertFormSubmit(static function (App $app) {
            $m = new Model();

            $options = ['yes please', 'woot'];

            $m->addField('opt1', ['values' => $options]);
            $m->addField('opt2', ['values' => $options]);
            $m->addField('opt3', ['values' => $options, 'nullable' => false]);
            $m->addField('opt3_z', ['values' => $options, 'nullable' => false]);
            $m->addField('opt4', ['values' => $options, 'required' => true]);
            $m->addField('opt4_z', ['values' => $options, 'required' => true]);

            $form = Form::addTo($app);
            $form->setModel($m->createEntity());

            return $form;
        }, ['opt1' => '2', 'opt3_z' => '0', 'opt4' => '', 'opt4_z' => '0'], null, function (string $formError) {
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
        $submitReached = false;
        $catchReached = false;
        try {
            try {
                $this->assertFormSubmit(static function (App $app) {
                    $m = new Model();
                    $m->addField('foo', ['nullable' => false]);
                    $m->addField('bar', ['nullable' => false]);

                    $form = Form::addTo($app);
                    $form->setModel($m->createEntity(), ['foo']);

                    return $form;
                }, ['foo' => 'x'], static function (Model $model) use (&$submitReached) {
                    $submitReached = true;
                    $model->set('bar', null);
                });
            } catch (UnhandledCallbackExceptionError $e) {
                $catchReached = true;
                self::assertSame('bar', $e->getPrevious()->getParams()['field']->shortName); // @phpstan-ignore-line

                $this->expectException(ValidationException::class);
                $this->expectExceptionMessage('Must not be null');

                throw $e->getPrevious();
            }
        } finally {
            self::assertTrue($submitReached);
            self::assertTrue($catchReached);
        }
    }

    public function testNoDisabledAttrWithHiddenType(): void
    {
        $input = new Form\Control\Line();
        $input->readOnly = true;
        $input->setApp($this->createApp());
        self::assertStringContainsString(' readonly="readonly"', $input->render());
        self::assertStringNotContainsString('disabled', $input->render());

        $input = new Form\Control\Line();
        $input->disabled = true;
        $input->readOnly = true;
        $input->setApp($this->createApp());
        self::assertStringContainsString(' disabled="disabled"', $input->render());
        self::assertStringNotContainsString('readonly', $input->render());

        $input = new Form\Control\Hidden();
        $input->disabled = true;
        $input->readOnly = true;
        $input->setApp($this->createApp());
        self::assertStringNotContainsString('disabled', $input->render());
        self::assertStringNotContainsString('readonly', $input->render());
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
        $input = $this->simulateViewCallback(function (ServerRequestInterface $request) {
            $app = $this->createApp(['request' => $request]);
            $input = Form\Control\Upload::addTo($app);

            return $input;
        }, function (Form\Control\Upload $input) {
            $request = $this->triggerCallback($input->getApp()->getRequest(), $input->cb);
            $request = $request->withParsedBody(['fUploadAction' => Form\Control\Upload::UPLOAD_ACTION]);

            return $request;
        });

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Missing onUpload callback');
        $input->render();
    }

    public function testUploadNoDeleteCallbackException(): void
    {
        $input = $this->simulateViewCallback(function (ServerRequestInterface $request) {
            $app = $this->createApp(['request' => $request]);
            $input = Form\Control\Upload::addTo($app);

            return $input;
        }, function (Form\Control\Upload $input) {
            $request = $this->triggerCallback($input->getApp()->getRequest(), $input->cb);
            $request = $request->withParsedBody(['fUploadAction' => Form\Control\Upload::DELETE_ACTION]);

            return $request;
        });

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Missing onDelete callback');
        $input->render();
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
