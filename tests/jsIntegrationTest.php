<?php

namespace atk4\ui\tests;

use atk4\ui\Button;
use atk4\ui\View;

class jsIntegrationTest extends \atk4\core\PHPUnit_AgileTestCase
{
    public function testIDIntegrity1()
    {
        $v = new Button(['icon' => 'pencil']);
        $html = $v->render();
        $this->assertNotNull($v->icon->id);

        $this->assertNotEquals($v->id, $v->icon->id);
    }

    public function testIDIntegrity2()
    {
        $v = new View(['ui' => 'buttons']);
        $b1 = $v->add(new Button());
        $b2 = $v->add(new Button());
        $html = $v->render();

        $this->assertNotEquals($b1->id, $b2->id);
    }

    /**
     * make sure that chain is crated correctly.
     */
    public function testBasicChain1()
    {
        $v = new Button(['id' => 'b']);
        $j = $v->js()->hide();
        $v->render();

        $this->assertEquals('$("#b").hide()', $j->jsRender());
    }

    /**
     * make sure that onReady chains are included in output.
     */
    public function testBasicChain2()
    {
        $v = new Button(['id' => 'b']);
        $j = $v->js(true)->hide();
        $v->getHTML();

        $this->assertEquals('<script>
$(function() {
  $("#b").hide();
})</script>', $v->getJS());
    }

    /**
     * make sure that js('event') chains are included in output with appropriate callback.
     */
    public function testBasicChain3()
    {
        $v = new Button(['id' => 'b']);
        $v->js('click')->hide();
        $v->getHTML();

        $this->assertEquals('<script>
$(function() {
  $("#b").bind("click",function() {
    $("#b").hide();
  });
})</script>', $v->getJS());
    }

    /**
     * make sure that on('event', js) chains are included in output.
     */
    public function testBasicChain4()
    {
        $bb = new View(['ui' => 'buttons']);
        $b1 = $bb->add(new Button(['id' => 'b1']));
        $b2 = $bb->add(new Button(['id' => 'b2']));

        $b1->on('click', $b2->js()->hide());
        $bb->getHTML();

        $this->assertEquals('<script>
$(function() {
  $("#b1").on("click",function(event) {
    event.preventDefault();
    event.stopPropagation();
    $("#b2").hide();
  });
})</script>', $bb->getJS());
    }
}
