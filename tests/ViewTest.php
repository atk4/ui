<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Data\Model;
use Atk4\Ui\AbstractView;
use Atk4\Ui\Callback;
use Atk4\Ui\Console;
use Atk4\Ui\Exception;
use Atk4\Ui\JsCallback;
use Atk4\Ui\JsSse;
use Atk4\Ui\Loader;
use Atk4\Ui\Modal;
use Atk4\Ui\Popup;
use Atk4\Ui\View;
use Atk4\Ui\VirtualPage;

class ViewTest extends TestCase
{
    use CreateAppTrait;

    public function testMultipleTimesRender(): void
    {
        $v = new View();
        $v->set('foo');

        $v->setApp($this->createApp());
        $a = $v->render();
        $b = $v->render();
        self::assertSame($a, $b);
    }

    public function testAddAfterRenderException(): void
    {
        $v = new View();
        $v->set('foo');

        $v->setApp($this->createApp());
        $v->render();

        $this->expectException(Exception::class);
        View::addTo($v);
    }

    public function testVoidTagRender(): void
    {
        $v = new View();
        $v->setApp($this->createApp());
        self::assertSame('<div id="atk"></div>', $v->render());

        $v = new View();
        $v->element = 'img';
        $v->setApp($this->createApp());
        self::assertSame('<img id="atk">', $v->render());
    }

    public function testAddDelayedInit(): void
    {
        $v = new View();
        $vInner = new View();

        $v->add($vInner);
        self::assertFalse($v->isInitialized());
        self::assertFalse($vInner->isInitialized());

        $vLayout = new View();
        $vLayout->setApp($this->createApp());
        $vLayout->add($v);

        self::assertTrue($v->isInitialized());
        self::assertTrue($vInner->isInitialized());
    }

    public function testAddDelayedAbstractViewInit(): void
    {
        $v = new class() extends AbstractView {};
        $vInner = new View();

        $v->add($vInner);
        self::assertFalse($v->isInitialized());
        self::assertFalse($vInner->isInitialized());

        $vLayout = new View();
        $vLayout->setApp($this->createApp());
        $vLayout->add($v);

        self::assertTrue($v->isInitialized());
        self::assertTrue($vInner->isInitialized());
    }

    public function testTooManyArgumentsConstructorError(): void
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Too many method arguments');
        new View([], []); // @phpstan-ignore-line
    }

    public function testTooManyArgumentsAddError(): void
    {
        $v = new View();
        $vInner = new View();

        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Too many method arguments');
        $v->add($vInner, [], []); // @phpstan-ignore-line
    }

    public function testTooManyArgumentsAbstractViewAddError(): void
    {
        $v = new class() extends AbstractView {};
        $vInner = new View();

        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Too many method arguments');
        $v->add($vInner, [], []); // @phpstan-ignore-line
    }

    public function testSetModelTwiceException(): void
    {
        $v = new View();
        $m1 = new Model();
        $m2 = new Model();
        $v->setModel($m1);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Different model is already set');
        $v->setModel($m2);
    }

    public function testSetSourceZeroKeyException(): void
    {
        $v = new View();
        $v->setSource(['a', 'b']);

        $v = new View();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Source data contains unsupported zero key');
        $v->setSource(['a', 2 => 'b']);
    }

    public function testSetException(): void
    {
        $v = new View();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Not sure what to do with argument');
        $v->set(1); // @phpstan-ignore-line
    }

    /**
     * @param class-string<View|Callback> $class
     *
     * @dataProvider provideSetNotClosureErrorCases
     */
    public function testSetNotClosureError(string $class): void
    {
        $v = new $class();

        $this->expectException(\Error::class);
        $this->expectExceptionMessage('$fx must be of type Closure');
        $v->set('strlen');
    }

    /**
     * @return iterable<list{class-string<View|Callback>}>
     */
    public function provideSetNotClosureErrorCases(): iterable
    {
        yield [Console::class];
        yield [JsCallback::class];
        yield [JsSse::class];
        yield [Loader::class];
        yield [Modal::class];
        yield [Popup::class];
        yield [VirtualPage::class];
    }

    /**
     * TODO remove the explicit exceptions and this test/provider once release 5.0 is made.
     *
     * @param class-string<View> $class
     *
     * @dataProvider provideSetNotOneArgumentExceptionCases
     */
    public function testSetNotOneArgumentException(string $class): void
    {
        $v = new $class();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Only one argument is needed by ' . preg_replace('~.+\\\\~', '', $class) . '::set()');
        $v->set(static function () {}, null); // @phpstan-ignore-line
    }

    /**
     * @return iterable<list{class-string<View>}>
     */
    public function provideSetNotOneArgumentExceptionCases(): iterable
    {
        yield [View::class];
        yield [Loader::class];
        yield [Modal::class];
        yield [Popup::class];
    }
}
