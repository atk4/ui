<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Ui\Button;
use Atk4\Ui\Exception;
use Atk4\Ui\Js\JsBlock;
use Atk4\Ui\JsCallback;
use Atk4\Ui\View;

class JsIntegrationTest extends TestCase
{
    use CreateAppTrait;

    public function testUniqueId1(): void
    {
        $v = new Button(['icon' => 'pencil']);
        $v->setApp($this->createApp());
        $v->render();

        self::assertNotEmpty($v->icon);
        self::assertNotEmpty($v->icon->name);
        self::assertNotSame($v->name, $v->icon->name);
    }

    public function testUniqueId2(): void
    {
        $v = new View(['ui' => 'buttons']);
        $b1 = Button::addTo($v);
        $b2 = Button::addTo($v);
        $v->setApp($this->createApp());
        $v->render();

        self::assertNotSame($b1->name, $b2->name);
    }

    public function testChainFalse(): void
    {
        $v = new Button(['name' => 'b']);
        $j = $v->js()->hide();
        $v->setApp($this->createApp());
        $v->render();

        self::assertSame('$(\'#b\').hide()', $j->jsRender());
    }

    public function testChainTrue(): void
    {
        $v = new Button(['name' => 'b']);
        $j = $v->js(true)->hide();
        $v->setApp($this->createApp());
        $v->renderAll();

        self::assertSame('(function () {
    $(\'#b\').hide();
})()', $v->getJs());
    }

    public function testChainClick(): void
    {
        $v = new Button(['name' => 'b']);
        $v->js('click')->hide();
        $v->setApp($this->createApp());
        $v->renderAll();

        self::assertSame('(function () {
    $(\'#b\').on(\'click\', function (event) {
        event.preventDefault();
        event.stopPropagation();
        $(this).hide();
    });
})()', $v->getJs());
    }

    public function testChainClickEmpty(): void
    {
        $v = new Button(['name' => 'b']);
        $v->js('click', null);
        $v->setApp($this->createApp());
        $v->renderAll();

        self::assertSame('(function () {
    $(\'#b\').on(\'click\', function (event) {
        event.preventDefault();
        event.stopPropagation();
        '
        . '$(this);' // this JS statement is not required
        . '
    });
})()', $v->getJs());
    }

    public function testChainNested(): void
    {
        $v = new View(['ui' => 'buttons']);
        $b1 = Button::addTo($v, ['name' => 'b1']);
        $b2 = Button::addTo($v, ['name' => 'b2']);

        $b1->on('click', new JsBlock([
            $b1->js()->hide(),
            $b2->js()->hide(),
            $b2->js()->hide(),
        ]), ['preventDefault' => false]);
        $b1->js(true)->data('x', 'y');
        $v->setApp($this->createApp());
        $v->renderAll();

        self::assertSame('(function () {
    $(\'#b1\').on(\'click\', function (event) {
        event.stopPropagation();
        $(\'#b1\').hide();
        $(\'#b2\').hide();
        $(\'#b2\').hide();
    });
    $(\'#b1\').data(\'x\', \'y\');
})()', $v->getJs());
    }

    public function testChainNullReturn(): void
    {
        $v = new View(['name' => 'v']);
        $js = $v->js();

        self::assertNotNull($v->js(true, null)); // @phpstan-ignore-line
        self::assertNull($v->js(true, $js)); // @phpstan-ignore-line
        self::assertNull($v->on('click', $js)); // @phpstan-ignore-line
    }

    public function testChainUnsupportedTypeException(): void
    {
        $v = new View();
        $v->setApp($this->createApp());
        $v->invokeInit();

        $js = $v->js();
        $js->data(['url' => JsCallback::addTo($v)]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('not renderable to JS');
        $js->jsRender();
    }

    public function testChainJsCallbackLazyExecuteRender(): void
    {
        $v = new View();
        $v->setApp($this->createApp());
        $v->invokeInit();
        $b = Button::addTo($v);

        $jsCallback = new class() extends JsCallback {
            public int $counter = 0;

            public function jsExecute(): JsBlock
            {
                ++$this->counter;

                return parent::jsExecute();
            }
        };
        $v->add($jsCallback);

        $b->on('click', $jsCallback);
        self::assertSame(0, $jsCallback->counter);

        $v->renderAll();
        self::assertSame(0, $jsCallback->counter);

        $v->getJs();
        self::assertSame(1, $jsCallback->counter);
    }
}
