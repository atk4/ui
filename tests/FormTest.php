<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Data\Field;
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

        $form->addControl('foo');

        self::assertInstanceOf(Form\Control::class, $form->getControl('foo'));
        self::assertSame($form->getControl('foo'), $form->layout->getControl('foo'));
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
    protected function assertFormSubmit(\Closure $createFormFx, array $postData, ?\Closure $submitFx = null, ?\Closure $checkExpectedErrorsFx = null): void
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
                $submitFx($form->entity);
            }
        });

        $form->renderToHtml();
        $res = AppFormTestMock::assertInstanceOf($form->getApp())->output;

        if ($checkExpectedErrorsFx !== null) {
            self::assertFalse($wasSubmitCalled);
            self::assertNotEmpty($res['atkjs']);
            $this->formError = $res['atkjs'];

            $checkExpectedErrorsFx($res['atkjs']);
        } else {
            self::assertTrue($wasSubmitCalled);
            self::assertSame('', $res['atkjs']);
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

            self::assertSame('John', $form->entity->get('name'));

            return $form;
        }, ['email' => 'john@yahoo.com', 'is_admin' => '1'], static function (Model $entity) {
            // field has default, but form send back empty value
            self::assertSame('', $entity->get('name'));

            self::assertSame('john@yahoo.com', $entity->get('email'));

            // security check, unspecified field must not be changed
            self::assertFalse($entity->get('is_admin'));
        });
    }

    public function testTextareaSubmit(): void
    {
        $this->assertFormSubmit(static function (App $app) {
            $form = Form::addTo($app);
            $form->addControl('foo');

            return $form;
        }, ['foo' => '0'], static function (Model $entity) {
            self::assertSame('0', $entity->get('foo'));
        });
    }

    protected function assertFormControlError(string $field, string $expectedError): void
    {
        $n = preg_match_all('~\.form\(\'add prompt\', \'([^\']*)\', \'([^\']*)\'\)~', $this->formError, $matchesAll, \PREG_SET_ORDER);
        self::assertGreaterThan(0, $n);
        $matched = false;
        foreach ($matchesAll as $matches) {
            if ($matches[1] === $field) {
                $matched = true;
                self::assertSame($expectedError, $matches[2]);
            }
        }

        self::assertTrue($matched);
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

            $m->addField('int', ['type' => 'integer']);

            $form = Form::addTo($app);
            $form->setModel($m->createEntity());

            return $form;
        }, [
            'opt1' => '2',
            'opt3_z' => '0',
            'opt4' => '',
            'opt4_z' => '0',
            'int' => '0x',
        ], null, function (string $formError) {
            // dropdown validates to make sure option is proper
            $this->assertFormControlError('opt1', 'Value is not one of the allowed values: 0, 1');

            // user didn't select any option here
            $this->assertFormControlNoErrors('opt2');

            // dropdown insists for value to be there
            $this->assertFormControlNoErrors('opt3');
            $this->assertFormControlNoErrors('opt3_z');
            $this->assertFormControlError('opt4', 'Must not be empty');
            $this->assertFormControlError('opt4_z', 'Must not be empty');

            $this->assertFormControlError('int', 'Must be numeric');
        });
    }

    public function testSubmitNonFormFieldError(): void
    {
        $submitReached = false;
        $catchReached = false;
        try {
            $this->assertFormSubmit(static function (App $app) {
                $m = new Model();
                $m->addField('foo', ['nullable' => false]);
                $m->addField('bar', ['nullable' => false]);

                $form = Form::addTo($app);
                $form->setModel($m->createEntity(), ['foo']);

                return $form;
            }, ['foo' => 'x'], static function (Model $entity) use (&$submitReached) {
                $submitReached = true;
                $entity->set('bar', null);
            });
        } catch (UnhandledCallbackExceptionError $e) {
            $catchReached = true;
            self::assertSame('bar', $e->getPrevious()->getParams()['field']->shortName); // @phpstan-ignore-line

            $this->expectException(ValidationException::class);
            $this->expectExceptionMessage('Must not be null');

            throw $e->getPrevious();
        } finally {
            self::assertTrue($submitReached);
            self::assertTrue($catchReached);
        }
    }

    public function testLoadPostConvertedWarningNotWrappedException(): void
    {
        $catchReached = false;
        try {
            $this->assertFormSubmit(static function (App $app) {
                $m = new Model();
                $m->addField('foo', new class() extends Field {
                    #[\Override]
                    public function normalize($value)
                    {
                        TestCase::assertSame('x', $value);

                        throw new \ErrorException('Converted PHP warning');
                    }
                });

                $form = Form::addTo($app);
                $form->setModel($m->createEntity());

                return $form;
            }, ['foo' => 'x']);
        } catch (UnhandledCallbackExceptionError $e) {
            $catchReached = true;

            $this->expectException(\ErrorException::class);
            $this->expectExceptionMessage('Converted PHP warning');

            throw $e->getPrevious();
        } finally {
            self::assertTrue($catchReached);
        }
    }

    public function testCreateControlException(): void
    {
        $form = new Form();
        $form->setApp($this->createApp());
        $form->invokeInit();

        $controlClass = get_class(new class() extends Form\Control {
            public static bool $firstCreate = true;

            public function __construct() // @phpstan-ignore-line
            {
                if (self::$firstCreate) {
                    self::$firstCreate = false;

                    return;
                }

                throw new Exception('x');
            }
        });

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unable to create form control');
        try {
            $form->addControl('foo', [$controlClass]);
        } catch (Exception $e) {
            self::assertSame(Exception::class, get_class($e->getPrevious()));
            self::assertSame('x', $e->getPrevious()->getMessage());

            throw $e;
        } finally {
            $controlClass::$firstCreate = true;
        }
    }

    public function testCreateControlConvertedWarningNotWrappedException(): void
    {
        $form = new Form();
        $form->setApp($this->createApp());
        $form->invokeInit();

        $controlClass = get_class(new class() extends Form\Control {
            public static bool $firstCreate = true;

            public function __construct() // @phpstan-ignore-line
            {
                if (self::$firstCreate) {
                    self::$firstCreate = false;

                    return;
                }

                throw new \ErrorException('Converted PHP warning');
            }
        });

        $this->expectException(\ErrorException::class);
        $this->expectExceptionMessage('Converted PHP warning');
        try {
            $form->addControl('foo', [$controlClass]);
        } finally {
            $controlClass::$firstCreate = true;
        }
    }

    public function testNoDisabledAttrWithHiddenType(): void
    {
        $input = new Form\Control\Line();
        $input->readOnly = true;
        $input->setApp($this->createApp());
        $input->shortName = 'i';
        self::assertStringContainsString(' readonly="readonly"', $input->renderToHtml());
        self::assertStringNotContainsString('disabled', $input->renderToHtml());

        $input = new Form\Control\Line();
        $input->disabled = true;
        $input->readOnly = true;
        $input->setApp($this->createApp());
        $input->shortName = 'i';
        self::assertStringContainsString(' disabled="disabled"', $input->renderToHtml());
        self::assertStringNotContainsString('readonly', $input->renderToHtml());

        $input = new Form\Control\Hidden();
        $input->disabled = true;
        $input->readOnly = true;
        $input->setApp($this->createApp());
        $input->shortName = 'i';
        self::assertStringNotContainsString('disabled', $input->renderToHtml());
        self::assertStringNotContainsString('readonly', $input->renderToHtml());
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
        $input->renderToHtml();
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
        $input->renderToHtml();
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
        $input->renderToHtml();
    }
}

class AppFormTestMock extends App
{
    /** @var string|array */
    public $output;

    #[\Override]
    public function terminate($output = ''): void
    {
        $this->output = $output;

        PhpstanUtil::fakeNeverReturn();
    }
}
