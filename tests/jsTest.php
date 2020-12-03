<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\AtkPhpunit;
use Atk4\Ui\Jquery;
use Atk4\Ui\JsChain;
use Atk4\Ui\JsExpression;
use Atk4\Ui\JsFunction;

class jsTest extends AtkPhpunit\TestCase
{
    /**
     * Test constructor.
     */
    public function testBasicExpressions()
    {
        $this->assertSame('2+2', (new JsExpression('2+2'))->jsRender());
        $this->assertSame('3+4', (new JsExpression('[]+[]', [3, 4]))->jsRender());
    }

    public function testNumbers()
    {
        if (PHP_INT_SIZE === 4) {
            $this->markTestIncomplete('Test is not supported on 32bit php');
        }

        foreach ([
            [10, '10'],
            [9007199254740991, '9007199254740991'],
            [9007199254740992, '"9007199254740992"'],
            [-9007199254740991, '-9007199254740991'],
            [-9007199254740992, '"-9007199254740992"'],
            [1.5, '1.5'],
            [false, 'false'],
            [true, 'true'],
        ] as [$in, $expected]) {
            $this->assertSame($expected, (new JsExpression('[]', [$in]))->jsRender());

            // test JSON renderer in App too
            // test extensively because of (possibly fragile) custom regex impl
            $app = (new \ReflectionClass(\Atk4\Ui\App::class))->newInstanceWithoutConstructor();
            $expectedRaw = json_decode($expected);
            foreach ([
                [$expectedRaw, $in], // direct value
                [[$expectedRaw => 'x'], [$in => 'x']], // as key
                [[$expectedRaw], [$in]], // as value in JSON array
                [['x' => $expectedRaw], ['x' => $in]], // as value in JSON object
            ] as [$expectedData, $inData]) {
                $this->assertSame(json_encode($expectedData), preg_replace('~\s+~', '', $app->encodeJson($inData)));

                // do not change any numbers to string in JSON/JS strings
                $inDataJson = json_encode($inData);
                $this->assertSame(json_encode(['x' => $inDataJson]), preg_replace('~\s+~', '', $app->encodeJson(['x' => $inDataJson])));
            }
        }
    }

    public function testNestedExpressions()
    {
        $this->assertSame(
            '10-(2+3)',
            (
                new JsExpression(
                    '[]-[]',
                    [
                        10,
                        new JsExpression(
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
        $c = new JsChain('$myInput');
        $c->getTextInRange('start', 'end');
        $this->assertSame('$myInput.getTextInRange("start","end")', $c->jsRender());
    }

    public function testChain2()
    {
        $c = new JsChain('$myInput');
        $c->getTextInRange(new JsExpression('getStart()'), 'end');
        $this->assertSame('$myInput.getTextInRange(getStart(),"end")', $c->jsRender());
    }

    public function testJquery()
    {
        $c = new Jquery('.mytag');
        $c->find('li')->first()->hide();

        $this->assertSame('$(".mytag").find("li").first().hide()', $c->jsRender());
    }

    public function testArgs()
    {
        $c = new Jquery('.mytag');
        $c->val((new Jquery('.othertag'))->val());

        $this->assertSame('$(".mytag").val($(".othertag").val())', $c->jsRender());
    }

    public function testComplex1()
    {
        // binding that maintains same height on
        $b1 = new Jquery('.box1');
        $b2 = new Jquery('.box2');

        $doc = new Jquery(new JsExpression('document'));
        $fx = $doc->ready(new JsFunction(null, [
            $b1->height($b2->height()),
        ]));

        $this->assertSame('$(document).ready(function() {
    $(".box1").height($(".box2").height());
  })', $fx->jsRender());
    }
}
