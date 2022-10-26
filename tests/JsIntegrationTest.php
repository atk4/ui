<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Ui\Button;
use Atk4\Ui\View;

class JsIntegrationTest extends TestCase
{
    public function testIdIntegrity1(): void
    {
        $v = new Button(['icon' => 'pencil']);
        $html = $v->render();
        static::assertNotNull($v->icon->name);

        static::assertNotSame($v->name, $v->icon->name);
    }

    public function testIdIntegrity2(): void
    {
        $v = new View(['ui' => 'buttons']);
        $b1 = Button::addTo($v);
        $b2 = Button::addTo($v);
        $html = $v->render();

        static::assertNotSame($b1->name, $b2->name);
    }

    /**
     * Make sure that chain is crated correctly.
     */
    public function testBasicChain1(): void
    {
        $v = new Button(['name' => 'b']);
        $j = $v->js()->hide();
        $v->render();

        static::assertSame('$(\'#b\').hide()', $j->jsRender());
    }

    /**
     * Make sure that onReady chains are included in output.
     */
    public function testBasicChain2(): void
    {
        $v = new Button(['name' => 'b']);
        $j = $v->js(true)->hide();
        $v->getHtml();

        static::assertSame('(function () {
    $(\'#b\').hide();
})()', $v->getJs());
    }

    /**
     * Make sure that js('event') chains are included in output with appropriate callback.
     */
    public function testBasicChain3(): void
    {
        $v = new Button(['name' => 'b']);
        $v->js('click')->hide();
        $v->getHtml();

        static::assertSame('(function () {
    $(\'#b\').bind(\'click\', function () {
        $(\'#b\').hide();
    });
})()', $v->getJs());
    }

    /**
     * Make sure that on('event', js) chains are included in output.
     */
    public function testBasicChain4(): void
    {
        $bb = new View(['ui' => 'buttons']);
        $b1 = Button::addTo($bb, ['name' => 'b1']);
        $b2 = Button::addTo($bb, ['name' => 'b2']);

        $b1->on('click', $b2->js()->hide());
        $bb->getHtml();

        static::assertSame('(function () {
    $(\'#b1\').on(\'click\', function (event) {
        event.preventDefault();
        event.stopPropagation();
        $(\'#b2\').hide();
    });
})()', $bb->getJs());
    }
}
