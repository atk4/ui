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
use Atk4\Ui\View\EntityTrait;
use Atk4\Ui\View\ModelTrait;
use Atk4\Ui\ViewWithContent;
use Atk4\Ui\VirtualPage;
use PHPUnit\Framework\Attributes\DataProvider;

class ViewTest extends TestCase
{
    use CreateAppTrait;

    public function testAddClass(): void
    {
        $v = new View();

        $v->addClass('a');
        self::assertSame(['a'], $v->class);

        $v->addClass('a b');
        self::assertSame(['a', 'b'], $v->class);

        $v->addClass(['a', 'c']);
        self::assertSame(['a', 'b', 'c'], $v->class);

        $v->removeClass('a');
        self::assertSame(['b', 'c'], $v->class);

        $v->removeClass('a b');
        self::assertSame(['c'], $v->class);

        $v->removeClass(['a']);
        self::assertSame(['c'], $v->class);
    }

    public function testSetStyle(): void
    {
        $v = new View();

        $v->setStyle('color', 'red');
        self::assertSame(['color' => 'red'], $v->style);

        $v->setStyle('margin', '0');
        self::assertSame(['color' => 'red', 'margin' => '0'], $v->style);

        $v->setStyle(['color' => 'green', 'padding' => '1px']);
        self::assertSame(['color' => 'green', 'margin' => '0', 'padding' => '1px'], $v->style);

        $v->removeStyle('color');
        self::assertSame(['margin' => '0', 'padding' => '1px'], $v->style);
    }

    public function testSetAttr(): void
    {
        $v = new View();

        $v->setAttr('foo', 'x');
        self::assertSame(['foo' => 'x'], $v->attr);

        $v->setAttr('bar', '0');
        self::assertSame(['foo' => 'x', 'bar' => '0'], $v->attr);

        $v->setAttr(['foo' => 'y', 'baz' => 'z']);
        self::assertSame(['foo' => 'y', 'bar' => '0', 'baz' => 'z'], $v->attr);

        $v->removeAttr('foo');
        self::assertSame(['bar' => '0', 'baz' => 'z'], $v->attr);

        $v->removeAttr('bar');
        self::assertSame(['baz' => 'z'], $v->attr);
    }

    public function testMultipleTimesRender(): void
    {
        $v = new ViewWithContent();
        $v->set('foo');

        $v->setApp($this->createApp());
        $a = $v->renderToHtml();
        $b = $v->renderToHtml();
        self::assertSame($a, $b);
    }

    public function testAddAfterRenderException(): void
    {
        $v = new ViewWithContent();
        $v->set('foo');

        $v->setApp($this->createApp());
        $v->renderAll();

        $this->expectException(Exception::class);
        View::addTo($v);
    }

    public function testVoidTagRender(): void
    {
        $v = new View();
        $v->setApp($this->createApp());
        self::assertSame('<div id="atk"></div>', $v->renderToHtml());

        $v = new View();
        $v->element = 'img';
        $v->setApp($this->createApp());
        self::assertSame('<img id="atk">', $v->renderToHtml());
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
        $v = new class extends AbstractView {};
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

    public function testSetModelDifferentException(): void
    {
        $v = new class {
            use ModelTrait;
        };
        $m1 = new Model();
        $m2 = new Model();
        $v->setModel($m1);
        $v->setModel($m1);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Different model is already set');
        $v->setModel($m2);
    }

    public function testSetEntityDifferentException(): void
    {
        $v = new class extends View {
            use EntityTrait;
        };
        $entity1 = (new Model())->createEntity();
        $entity2 = (new Model())->createEntity();
        $v->setEntity($entity1);
        $v->setEntity($entity1);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Different entity is already set');
        $v->setEntity($entity2);
    }

    public function testSetSourceZeroKeyException(): void
    {
        $v = new class {
            use ModelTrait;
        };
        $v->setSource(['a', 'b']);

        $vClass = get_class($v);
        $v = new $vClass();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Source data contains unsupported zero key');
        $v->setSource(['a', 2 => 'b']);
    }

    public function testSetException(): void
    {
        $v = new ViewWithContent();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Not sure what to do with argument');
        $v->set(1); // @phpstan-ignore argument.type
    }

    /**
     * @param class-string<View|Callback> $class
     *
     * @dataProvider provideSetNotClosureErrorCases
     */
    #[DataProvider('provideSetNotClosureErrorCases')]
    public function testSetNotClosureError(string $class): void
    {
        $v = new $class();

        $this->expectException(\Error::class);
        $this->expectExceptionMessage('$fx must be of type Closure');
        $v->set('strlen'); // @phpstan-ignore argument.type, argument.templateType
    }

    /**
     * @return iterable<list<mixed>>
     */
    public static function provideSetNotClosureErrorCases(): iterable
    {
        yield [Console::class];
        yield [JsCallback::class];
        yield [JsSse::class];
        yield [Loader::class];
        yield [Modal::class];
        yield [Popup::class];
        yield [VirtualPage::class];
    }

    public function testJsCallbackGetUrlException(): void
    {
        $v = new JsCallback();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Do not use getUrl on JsCallback, use getJsUrl()');
        $v->getUrl();
    }
}
