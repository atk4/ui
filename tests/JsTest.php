<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Ui\App;
use Atk4\Ui\Jquery;
use Atk4\Ui\JsChain;
use Atk4\Ui\JsExpression;
use Atk4\Ui\JsFunction;

class JsTest extends TestCase
{
    public function testBasicExpressions(): void
    {
        static::assertSame('2 + 2', (new JsExpression('2 + 2'))->jsRender());
        static::assertSame('3 + 4', (new JsExpression('[] + []', [3, 4]))->jsRender());
    }

    public function testStrings(): void
    {
        static::assertSame('\'\\\'\', \'"\', \'\n\'', (new JsExpression('[], [], []', ['\'', '"', "\n"]))->jsRender());
        static::assertSame('\'\\\'a"b\\\\\\\'c\\\\" \\\'"\'', (new JsExpression('[]', ['\'a"b\\\'c\\" \'"']))->jsRender());
    }

    public function testNumbers(): void
    {
        if (\PHP_INT_SIZE === 4) {
            static::markTestIncomplete('Test is not supported on 32bit php');
        }

        $longStrBase = '"a":10,"b":9007199254740992,x="\"c\":10,\"d\":9007199254740992,"';
        $longStr = str_repeat($longStrBase, intdiv(isset($_ENV['CI']) && ($_ENV['GITHUB_EVENT_NAME'] ?? null) === 'cron' ? 5_000_000 : 500_000, strlen($longStrBase)));
        foreach ([
            ['10', '"10"'],
            [10, '10'],
            [9007199254740991, '9007199254740991'],
            [9007199254740992, '"9007199254740992"'],
            [-9007199254740991, '-9007199254740991'],
            [-9007199254740992, '"-9007199254740992"'],
            [1.5, '1.5'],
            [false, 'false'],
            [true, 'true'],
            // verify if regex accepts big input and does not fail with backtrack limit
            [$longStrBase, json_encode($longStrBase)],
            [$longStr, json_encode($longStr)],
        ] as [$in, $expected]) {
            $jsRendered = (new JsExpression('[]', [$in]))->jsRender();
            if (substr($jsRendered, 0, 1) === '\'') {
                $jsRendered = '"' . str_replace('"', '\\"', substr($jsRendered, 1, -1)) . '"';
            }
            static::assertSame($expected, $jsRendered);

            // test JSON renderer in App too
            // test extensively because of complex custom regex impl
            $app = (new \ReflectionClass(App::class))->newInstanceWithoutConstructor();
            $expectedRaw = json_decode($expected);
            foreach ([
                [$expectedRaw, $in], // direct value
                [[(string) $expectedRaw => 'x'], [(string) $in => 'x']], // as key
                [[$expectedRaw], [$in]], // as value in JSON array
                [['x' => $expectedRaw], ['x' => $in]], // as value in JSON object
            ] as [$expectedData, $inData]) {
                static::assertSame(json_encode($expectedData), preg_replace('~\s+~', '', $app->encodeJson($inData)));

                // do not change any numbers to string in JSON/JS strings
                $inDataJson = json_encode($inData);
                static::assertSame(json_encode(['x' => $inDataJson]), preg_replace('~\s+~', '', $app->encodeJson(['x' => $inDataJson])));
            }
        }
    }

    public function testNestedExpressions(): void
    {
        static::assertSame(
            '10-(2 + 3)',
            (new JsExpression(
                '[]-[]',
                [10, new JsExpression('[a] + [b]', ['a' => 2, 'b' => 3])]
            ))->jsRender()
        );
    }

    public function testChain1(): void
    {
        $c = new JsChain('$myInput');
        $c->getTextInRange('start', 'end'); // @phpstan-ignore-line
        static::assertSame('$myInput.getTextInRange(\'start\', \'end\')', $c->jsRender());
    }

    public function testChain2(): void
    {
        $c = new JsChain('$myInput');
        $c->getTextInRange(new JsExpression('getStart()'), 'end'); // @phpstan-ignore-line
        static::assertSame('$myInput.getTextInRange(getStart(), \'end\')', $c->jsRender());
    }

    public function testJquery(): void
    {
        $c = new Jquery('.mytag');
        $c->find('li')->first()->hide();

        static::assertSame('$(\'.mytag\').find(\'li\').first().hide()', $c->jsRender());
    }

    public function testArgs(): void
    {
        $c = new Jquery('.mytag');
        $c->val((new Jquery('.othertag'))->val());

        static::assertSame('$(\'.mytag\').val($(\'.othertag\').val())', $c->jsRender());
    }

    public function testComplex1(): void
    {
        // binding that maintains same height on
        $b1 = new Jquery('.box1');
        $b2 = new Jquery('.box2');

        $doc = new Jquery(new JsExpression('document'));
        $fx = $doc->ready(new JsFunction([], [
            $b1->height($b2->height()),
        ]));

        static::assertSame('$(document).ready(function () {
        $(\'.box1\').height($(\'.box2\').height());
    })', $fx->jsRender());
    }
}
