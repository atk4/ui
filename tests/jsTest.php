<?php

namespace atk4\ui\tests;

use atk4\core\AtkPhpunit;
use atk4\ui\jQuery;
use atk4\ui\jsChain;
use atk4\ui\jsExpression;
use atk4\ui\jsFunction;

class jsTest extends AtkPhpunit\TestCase
{
    /**
     * Test constructor.
     */
    public function testBasicExpressions()
    {
        $this->assertSame('2+2', (new jsExpression('2+2'))->jsRender());
        $this->assertSame('3+4', (new jsExpression('[]+[]', [3, 4]))->jsRender());
    }

    public function testNestedExpressions()
    {
        $this->assertSame(
            '10-(2+3)',
            (
                new jsExpression(
                    '[]-[]',
                    [
                        10,
                        new jsExpression(
                            '[a]+[b]',
                            ['a' => 2, 'b' => 3]
                        ),
                    ]
                )
            )->jsRender()
        );
    }

    public function testChain1()
    {
        $c = new jsChain('$myInput');
        $c->getTextInRange('start', 'end');
        $this->assertSame('$myInput.getTextInRange("start","end")', $c->jsRender());
    }

    public function testChain2()
    {
        $c = new jsChain('$myInput');
        $c->getTextInRange(new jsExpression('getStart()'), 'end');
        $this->assertSame('$myInput.getTextInRange(getStart(),"end")', $c->jsRender());
    }

    public function testjQuery()
    {
        $c = new jQuery('.mytag');
        $c->find('li')->first()->hide();

        $this->assertSame('$(".mytag").find("li").first().hide()', $c->jsRender());
    }

    public function testArgs()
    {
        $c = new jQuery('.mytag');
        $c->val((new jQuery('.othertag'))->val());

        $this->assertSame('$(".mytag").val($(".othertag").val())', $c->jsRender());
    }

    public function testComplex1()
    {
        // binding that maintains same height on
        $b1 = new jQuery('.box1');
        $b2 = new jQuery('.box2');

        $doc = new jQuery(new jsExpression('document'));
        $fx = $doc->ready(new jsFunction(null, [
            $b1->height($b2->height()),
        ]));

        $this->assertSame('$(document).ready(function() {
    $(".box1").height($(".box2").height());
  })', $fx->jsRender());
    }
}
