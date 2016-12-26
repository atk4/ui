<?php

namespace atk4\ui\tests;

use \atk4\ui\jQuery;
use \atk4\ui\jsExpression;
use \atk4\ui\jsMapper;
use \atk4\ui\jsFunction;

class jsTest extends \atk4\core\PHPUnit_AgileTestCase
{
    /**
     * Test constructor.
     */
    public function testBasicExpressions()
    {

        $this->assertEquals('2+2', (new jsExpression('2+2'))->jsRender());
        $this->assertEquals('3+4', (new jsExpression('[]+[]', [3,4]))->jsRender());
    }

    public function testNestedExpressions()
    {
        $this->assertEquals(
            '10-(2+3)', 
            (
                new jsExpression(
                    '[]-[]', 
                    [
                        10,
                        new jsExpression(
                            '[a]+[b]',
                            ['a'=>2, 'b'=>3]
                        )
                    ]
                )
            )->jsRender()
        );
    }

    public function testChain1()
    {
        $c = new jsMapper('$myInput');
        $c->getTextInRange('start', 'end');
        $this->assertEquals('$myInput.getTextInRange("start","end")', $c->jsRender());
    }

    public function testChain2()
    {
        $c = new jsMapper('$myInput');
        $c->getTextInRange(new jsExpression('getStart()'), 'end');
        $this->assertEquals('$myInput.getTextInRange(getStart(),"end")', $c->jsRender());
    }

    public function testjQuery()
    {
        $c = new jQuery('.mytag');
        $c->find('li')->first()->hide();

        $this->assertEquals('$(".mytag").find("li").first().hide()', $c->jsRender());
    }

    public function testArgs()
    {
        $c = new jQuery('.mytag');
        $c->val((new jQuery('.othertag'))->val());

        $this->assertEquals('$(".mytag").val($(".othertag").val())', $c->jsRender());
    }

    public function testComplex1()
    {
        // binding that maintains same height on 
        $b1 = new jQuery('.box1');
        $b2 = new jQuery('.box2');

        $doc = new jQuery(new jsExpression('document'));
        $fx = $doc->ready(new jsFunction(null, [
            $b1->height($b2->height())
        ]));

        $this->assertEquals('$(document).ready(function() {
$(".box1").height($(".box2").height());
})', $fx->jsRender());
    }
}

