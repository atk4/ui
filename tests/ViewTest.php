<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Ui\AbstractView;
use Atk4\Ui\Exception;
use Atk4\Ui\View;

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

    public function testTooManyArgumentsConstructorError(): void
    {
        $this->expectException(\Error::class);
        new View([], []); // @phpstan-ignore-line
    }

    public function testTooManyArgumentsAddError(): void
    {
        $v = new View();
        $vInner = new View();

        $this->expectException(\Error::class);
        $v->add($vInner, null, []); // @phpstan-ignore-line
    }

    public function testTooManyArgumentsAdd2Error(): void
    {
        $v = new class() extends AbstractView { };
        $vInner = new View();

        $this->expectException(\Error::class);
        $v->add($vInner, null, []); // @phpstan-ignore-line
    }
}
