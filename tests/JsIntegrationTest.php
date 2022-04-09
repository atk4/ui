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
        $this->assertNotNull($v->icon->name);

        $this->assertNotSame($v->name, $v->icon->name);
    }

    public function testIdIntegrity2(): void
    {
        $v = new View(['ui' => 'buttons']);
        $b1 = Button::addTo($v);
        $b2 = Button::addTo($v);
        $html = $v->render();

        $this->assertNotSame($b1->name, $b2->name);
    }

    /**
     * make sure that chain is crated correctly.
     */
    public function testBasicChain1(): void
    {
        $v = new Button(['name' => 'b']);
        $j = $v->js()->hide();
        $v->render();

        $this->assertSame('$("#b").hide()', $j->jsRender());
    }

    /**
     * make sure that onReady chains are included in output.
     */
    public function testBasicChain2(): void
    {
        $v = new Button(['name' => 'b']);
        $j = $v->js(true)->hide();
        $v->getHtml();

        $this->assertSame('$(function() {
  $("#b").hide();
})', $v->getJs());
    }

    /**
     * make sure that js('event') chains are included in output with appropriate callback.
     */
    public function testBasicChain3(): void
    {
        $v = new Button(['name' => 'b']);
        $v->js('click')->hide();
        $v->getHtml();

        $this->assertSame('$(function() {
  $("#b").bind("click",function() {
    $("#b").hide();
  });
})', $v->getJs());
    }

    /**
     * make sure that on('event', js) chains are included in output.
     */
    public function testBasicChain4(): void
    {
        $bb = new View(['ui' => 'buttons']);
        $b1 = Button::addTo($bb, ['name' => 'b1']);
        $b2 = Button::addTo($bb, ['name' => 'b2']);

        $b1->on('click', $b2->js()->hide());
        $bb->getHtml();

        $this->assertSame('$(function() {
  $("#b1").on("click",function(event) {
    event.preventDefault();
    event.stopPropagation();
    $("#b2").hide();
  });
})', $bb->getJs());
    }
}
