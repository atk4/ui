<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests\UserAction;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Data\Model;
use Atk4\Ui\Exception;
use Atk4\Ui\UserAction\JsCallbackExecutor;

class JsCallbackExecutorTest extends TestCase
{
    public function testPrepareRenderButtonTooManyArgumentsException(): void
    {
        $m = new Model();
        $m->addUserAction('foo', [
            'args' => [
                'x' => ['type' => 'integer'],
            ],
        ]);

        $executor = new JsCallbackExecutor();
        $executor->setAction($m->getUserAction('foo'));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('URL arguments does not match user action arguments');
        $executor->executeModelAction();
    }
}
