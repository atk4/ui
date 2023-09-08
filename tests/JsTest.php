<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Ui\App;
use Atk4\Ui\Exception;
use Atk4\Ui\Js\Jquery;
use Atk4\Ui\Js\JsBlock;
use Atk4\Ui\Js\JsChain;
use Atk4\Ui\Js\JsExpression;
use Atk4\Ui\Js\JsFunction;

class JsTest extends TestCase
{
    protected function createAppWithoutConstructor(): App
    {
        return (new \ReflectionClass(App::class))->newInstanceWithoutConstructor();
    }

    public function testBasicExpressions(): void
    {
        self::assertSame('2 + 2', (new JsExpression('2 + 2'))->jsRender());
        self::assertSame('3 + 4', (new JsExpression('[] + []', [3, 4]))->jsRender());
    }

    public function testStrings(): void
    {
        self::assertSame('\'\\\'\', \'"\', \'\n\'', (new JsExpression('[], [], []', ['\'', '"', "\n"]))->jsRender());
        self::assertSame('\'\\\'a"b\\\\\\\'c\\\\" \\\'"\'', (new JsExpression('[]', ['\'a"b\\\'c\\" \'"']))->jsRender());
    }

    public function testNumbers(): void
    {
        if (\PHP_INT_SIZE === 4) {
            self::markTestIncomplete('Test is not supported on 32bit php');
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
            self::assertSame($expected, $jsRendered);

            // test JSON renderer in App too
            // test extensively because of complex custom regex impl
            $app = $this->createAppWithoutConstructor();
            $expectedRaw = json_decode($expected);
            foreach ([
                [$expectedRaw, $in], // direct value
                [[(string) $expectedRaw => 'x'], [(string) $in => 'x']], // as key
                [[$expectedRaw], [$in]], // as value in JSON array
                [['x' => $expectedRaw], ['x' => $in]], // as value in JSON object
            ] as [$expectedData, $inData]) {
                self::assertSame(json_encode($expectedData), preg_replace('~\s+~', '', $app->encodeJson($inData)));

                // do not change any numbers to string in JSON/JS strings
                $inDataJson = json_encode($inData);
                self::assertSame(json_encode(['x' => $inDataJson]), preg_replace('~\s+~', '', $app->encodeJson(['x' => $inDataJson])));
            }
        }
    }

    public function testJsonEncodeObjectException(): void
    {
        $app = $this->createAppWithoutConstructor();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Object to JSON encode is not supported');
        $app->encodeJson(new \stdClass());
    }

    public function testJsonEncodeArrayWithObjectException(): void
    {
        $app = $this->createAppWithoutConstructor();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Object to JSON encode is not supported');
        $app->encodeJson([[0, new \stdClass()]]);
    }

    public function testNestedExpressions(): void
    {
        self::assertSame(
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
        self::assertSame('$myInput.getTextInRange(\'start\', \'end\')', $c->jsRender());
    }

    public function testChain2(): void
    {
        $c = new JsChain('$myInput');
        $c->getTextInRange(new JsExpression('getStart()'), 'end'); // @phpstan-ignore-line
        self::assertSame('$myInput.getTextInRange(getStart(), \'end\')', $c->jsRender());
    }

    public function testChainNameStartingWithDigit(): void
    {
        $c = new JsChain('$myInput');
        $c->{'1x'}(2);
        self::assertSame('$myInput[\'1x\'](2)', $c->jsRender());
    }

    public function testChainNameWithDot(): void
    {
        $c = new JsChain('$myInput');
        $c->{'x.y'}(2);
        self::assertSame('$myInput[\'x.y\'](2)', $c->jsRender());
    }

    public function testJquery(): void
    {
        $c = new Jquery('.mytag');
        $c->find('li')->first()->hide();

        self::assertSame('$(\'.mytag\').find(\'li\').first().hide()', $c->jsRender());
    }

    public function testArgs(): void
    {
        $c = new Jquery('.mytag');
        $c->val((new Jquery('.othertag'))->val());

        self::assertSame('$(\'.mytag\').val($(\'.othertag\').val())', $c->jsRender());
    }

    public function testComplex1(): void
    {
        // binding that maintains same height on
        $b1 = new Jquery('.box1');
        $b2 = new Jquery('.box2');

        $doc = new Jquery(new JsExpression('document'));
        $fx = $doc->first(new JsFunction([], [
            $b1->height($b2->height()),
        ]));

        self::assertSame(<<<'EOF'
            $(document).first(function () {
                $('.box1').height($('.box2').height());
            })
            EOF, $fx->jsRender());
    }

    public function testTagNotDefinedRenderException(): void
    {
        $js = new JsExpression('[foo]', ['foo']);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Tag is not defined in template');
        $js->jsRender();
    }

    public function testUnsupportedTypeRenderException(): void
    {
        $js = new JsExpression('{}', [new \stdClass()]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('not renderable to JS');
        $js->jsRender();
    }

    public function testBlockBasic(): void
    {
        $statements = [
            new JsExpression('a()'),
            new JsExpression('b([])', ['foo']),
        ];

        $jsBlock = new JsBlock($statements);

        self::assertSame($statements, $jsBlock->getStatements());
        self::assertSame(<<<'EOF'
            a();
            b('foo');
            EOF, $jsBlock->jsRender());
    }

    public function testBlockEndSemicolon(): void
    {
        $jsBlock = new JsBlock([
            new JsExpression('a()'),
            new JsExpression('b();'),
            new JsExpression('let fx = () => { a(); b(); }'),
            new JsExpression(''),
            new JsBlock(),
            new class() extends JsBlock {
                public function jsRender(): string
                {
                    return 'if (foo) { a(); }';
                }
            },
        ]);

        self::assertSame(<<<'EOF'
            a();
            b();
            let fx = () => { a(); b(); };
            if (foo) { a(); }
            EOF, $jsBlock->jsRender());
    }

    public function testBlockInExpression(): void
    {
        $jsExpression = new JsExpression('a()');
        $jsBlock = new JsBlock([$jsExpression]);
        $jsExpressionWithJsBlock = new JsExpression('[]', [$jsBlock]);

        self::assertSame('(a())', (new JsExpression('[]', [$jsExpression]))->jsRender());
        self::assertSame('a();', (new JsExpression('[]', [$jsBlock]))->jsRender());
        self::assertSame('a();', (new JsExpression('[]', [$jsExpressionWithJsBlock]))->jsRender());
    }

    public function testBlockInvalidStringTypeException(): void
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage((\PHP_MAJOR_VERSION === 7 ? 'must implement interface' : 'must be of type') . ' Atk4\Ui\Js\JsExpressionable, string given');
        new JsBlock(['a()']); // @phpstan-ignore-line
    }

    public function testBlockInvalidArrayTypeException(): void
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage((\PHP_MAJOR_VERSION === 7 ? 'must implement interface' : 'must be of type') . ' Atk4\Ui\Js\JsExpressionable, array given');
        new JsBlock([[]]); // @phpstan-ignore-line
    }
}
