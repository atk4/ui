<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests\Form\Control;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Data\Model;
use Atk4\Ui\Exception;
use Atk4\Ui\Form;
use Atk4\Ui\Tests\CreateAppTrait;
use Atk4\Ui\UserAction\JsCallbackExecutor;

class InputTest extends TestCase
{
    use CreateAppTrait;

    public function testPrepareRenderButtonTooManyArgumentsException(): void
    {
        $form = new Form();
        $form->setApp($this->createApp());
        $form->invokeInit();

        $m = new Model();
        $m->addUserAction('foo', [
            'args' => [
                'x' => ['type' => 'integer'],
                'y' => ['type' => 'integer'],
            ],
        ]);

        $executor = new JsCallbackExecutor();
        $executor->setAction($m->getUserAction('foo'));

        $form->addControl('foo', [Form\Control\Input::class, 'action' => $executor]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Input form control supports user action with zero or one argument only');
        $form->renderAll();
    }
}
