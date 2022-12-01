<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Ui\Button;
use Atk4\Ui\View;

class JsIntegrationTest extends TestCase
{
    public function testUniqueId1(): void
    {
        $v = new Button(['icon' => 'pencil']);
        $v->render();

        static::assertNotEmpty($v->icon);
        static::assertNotEmpty($v->icon->name);
        static::assertNotSame($v->name, $v->icon->name);
    }

    public function testUniqueId2(): void
    {
        $v = new View(['ui' => 'buttons']);
        $b1 = Button::addTo($v);
        $b2 = Button::addTo($v);
        $v->render();

        static::assertNotSame($b1->name, $b2->name);
    }

    public function testBasicChainFalse(): void
    {
        $v = new Button(['name' => 'b']);
        $j = $v->js()->hide();
        $v->render();

        static::assertSame('$(\'#b\').hide()', $j->jsRender());
    }

    public function testBasicChainTrue(): void
    {
        $v = new Button(['name' => 'b']);
        $j = $v->js(true)->hide();
        $v->getHtml();

        static::assertSame('(function () {
    $(\'#b\').hide();
})()', $v->getJs());
    }

    public function testBasicChainClick(): void
    {
        $v = new Button(['name' => 'b']);
        $v->js('click')->hide();
        $v->getHtml();

        static::assertSame('(function () {
    $(\'#b\').on(\'click\', function (event) {
        event.preventDefault();
        event.stopPropagation();
        $(this).hide();
    });
})()', $v->getJs());
    }

    public function testBasicChainClickEmpty(): void
    {
        $v = new Button(['name' => 'b']);
        $v->js('click', null);
        $v->getHtml();

        static::assertSame('(function () {
    $(\'#b\').on(\'click\', function (event) {
        event.preventDefault();
        event.stopPropagation();
    });
})()', $v->getJs());
    }

    public function testBasicChainNested(): void
    {
        $bb = new View(['ui' => 'buttons']);
        $b1 = Button::addTo($bb, ['name' => 'b1']);
        $b2 = Button::addTo($bb, ['name' => 'b2']);

        $b1->on('click', [
            'preventDefault' => false,
            $b1->js()->hide(),
            $b2->js()->hide(),
            $b2->js()->hide(),
        ]);
        $b1->js(true)->data('x', 'y');
        $bb->getHtml();

        static::assertSame('(function () {
    $(\'#b1\').on(\'click\', function (event) {
        event.stopPropagation();
        $(\'#b1\').hide();
        $(\'#b2\').hide();
        $(\'#b2\').hide();
    });
    $(\'#b1\').data(\'x\', \'y\');
})()', $bb->getJs());
    }
}
