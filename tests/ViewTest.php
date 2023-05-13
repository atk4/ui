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
        static::assertSame($a, $b);
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
        static::assertSame('<div id="atk"></div>', $v->render());

        $v = new View();
        $v->element = 'img';
        $v->setApp($this->createApp());
        static::assertSame('<img id="atk">', $v->render());
    }

    public function testAddDelayedInit(): void
    {
        $v = new View();
        $vInner = new View();

        $v->add($vInner);
        static::assertFalse($v->isInitialized());
        static::assertFalse($vInner->isInitialized());

        $vLayout = new View();
        $vLayout->setApp($this->createApp());
        $vLayout->add($v);

        static::assertTrue($v->isInitialized());
        static::assertTrue($vInner->isInitialized());
    }

    public function testAddDelayedAbstractViewInit(): void
    {
        $v = new class() extends AbstractView { };
        $vInner = new View();

        $v->add($vInner);
        static::assertFalse($v->isInitialized());
        static::assertFalse($vInner->isInitialized());

        $vLayout = new View();
        $vLayout->setApp($this->createApp());
        $vLayout->add($v);

        static::assertTrue($v->isInitialized());
        static::assertTrue($vInner->isInitialized());
    }

    public function testTooManyArgumentsConstructorError(): void
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Too many method arguments');
        new View([], []);
    }

    public function testTooManyArgumentsAddError(): void
    {
        $v = new View();
        $vInner = new View();

        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Too many method arguments');
        $v->add($vInner, [], []);
    }

    public function testTooManyArgumentsAbstractViewAddError(): void
    {
        $v = new class() extends AbstractView { };
        $vInner = new View();

        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Too many method arguments');
        $v->add($vInner, [], []);
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
        $v->set(1);
    }

    /**
     * @param class-string<View|Callback> $class
     *
     * @dataProvider setNotClosureProvider
     */
    public function testSetNotClosureError(string $class): void
    {
        $v = new $class();

        $this->expectException(\Error::class);
        $this->expectExceptionMessage('$fx must be of type Closure');
        $v->set('strlen');
    }

    /**
     * @return list<list<class-string<View|Callback>>>
     */
    public function setNotClosureProvider(): array
    {
        return [
            [Console::class],
            [JsCallback::class],
            [Loader::class],
            [Modal::class],
            [Popup::class],
            [VirtualPage::class],
        ];
    }

    /**
     * @param class-string<View> $class
     *
     * @dataProvider setNotOneArgumentExceptionProvider
     */
    public function testSetNotOneArgumentException(string $class): void
    {
        $v = new $class();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Only one argument is needed by ' . preg_replace('~.+\\\\~', '', $class) . '::set()');
        $v->set(function () {}, null);
    }

    /**
     * @return list<list<class-string<View>>>
     */
    public function setNotOneArgumentExceptionProvider(): array
    {
        return [
            [Loader::class],
            [Modal::class],
            [Popup::class],
        ];
    }
}
