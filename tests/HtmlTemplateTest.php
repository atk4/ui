<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Ui\Exception;
use Atk4\Ui\HtmlTemplate;

class HtmlTemplateTest extends TestCase
{
    protected function assertSameTemplate(string $expectedTemplateStr, HtmlTemplate $template): void
    {
        $expectedTemplate = new HtmlTemplate($expectedTemplateStr);
        static::assertSame($expectedTemplate->toLoadableString(), $template->toLoadableString());
        static::assertSame($expectedTemplate->renderToHtml(), $template->renderToHtml());

        // TODO test if all tag trees are reachable
    }

    protected function assertSameTagTree(string $expectedTemplateStr, HtmlTemplate\TagTree $tagTree): void
    {
        $this->assertSameTemplate(
            $expectedTemplateStr,
            $tagTree->getParentTemplate()->cloneRegion($tagTree->getTag())
        );
    }

    public function testBasicInit(): void
    {
        $t = new HtmlTemplate('hello, {foo}world{/}');
        $t->set('foo', 'bar');

        $this->assertSameTemplate('hello, {foo}bar{/}', $t);
    }

    public function testGetTagTree(): void
    {
        $t = new HtmlTemplate('{foo}hello{/}, cruel {bar}world{/}. {foo}hello{/}');
        $this->assertSameTagTree('{foo}hello{/}, cruel {bar}world{/}. {foo}hello{/}', $t->getTagTree('_top'));

        $t = new HtmlTemplate('{foo}hello{/}, cruel {bar}world{/}. {foo}hello{/}');
        $tagTreeFoo = $t->getTagTree('foo');
        $this->assertSameTagTree('hello', $tagTreeFoo);

        $tagTreeFoo->getChildren()[0]->set('good bye');
        $this->assertSameTemplate('{foo}good bye{/}, cruel {bar}world{/}. {foo}good bye{/}', /* not possible with dual renderer $t */ $tagTreeFoo->getParentTemplate());
    }

    public function testGetTagRefNotFoundException(): void
    {
        $t = new HtmlTemplate('{foo}hello{/}');

        $this->expectException(Exception::class);
        $t->getTagTree('bar');
    }

    public function testLoadFromFileNonExistentFileException(): void
    {
        $t = new HtmlTemplate();

        $this->expectException(Exception::class);
        $t->loadFromFile(__DIR__ . '/bad_template_file');
    }

    public function testTryLoadFromFileNonExistentFileException(): void
    {
        $t = new HtmlTemplate();
        static::assertFalse($t->tryLoadFromFile(__DIR__ . 'bad_template_file'));
    }

    public function testHasTag(): void
    {
        $t = new HtmlTemplate('{foo}hello{/}, cruel {bar}world{/}. {foo}hello{/}');
        static::assertTrue($t->hasTag('foo'));
        static::assertTrue($t->hasTag(['foo', 'bar']));
        static::assertFalse($t->hasTag(['foo', 'bar', 'non_existent_tag']));
    }

    public function testSetBadTypeException(): void
    {
        $t = new HtmlTemplate('{foo}hello{/} guys');

        $this->expectException(Exception::class);
        $t->set('foo', new \stdClass()); // @phpstan-ignore-line
    }

    public function testSetAppendDel(): void
    {
        $t = new HtmlTemplate('{foo}hello{/} guys');

        // del tests
        $t->del('foo');
        $this->assertSameTemplate('{$foo} guys', $t);
        $t->tryDel('non_existent_tag');
        $this->assertSameTemplate('{$foo} guys', $t);

        // set tests
        $t->set('foo', 'Hello');
        $this->assertSameTemplate('{foo}Hello{/} guys', $t);
        $t->set('foo', 'Hi');
        $this->assertSameTemplate('{foo}Hi{/} guys', $t);
        $t->dangerouslySetHtml('foo', '<b>Hi</b>');
        $this->assertSameTemplate('{foo}<b>Hi</b>{/} guys', $t);
        $t->trySet('non_existent_tag', 'ignore this');
        $this->assertSameTemplate('{foo}<b>Hi</b>{/} guys', $t);
        $t->tryDangerouslySetHtml('non_existent_tag', '<b>ignore</b> this');
        $this->assertSameTemplate('{foo}<b>Hi</b>{/} guys', $t);

        // append tests
        $t->set('foo', 'Hi');
        $this->assertSameTemplate('{foo}Hi{/} guys', $t);
        $t->append('foo', ' and');
        $this->assertSameTemplate('{foo}Hi and{/} guys', $t);
        $t->dangerouslyAppendHtml('foo', ' <b>welcome</b> my');
        $this->assertSameTemplate('{foo}Hi and <b>welcome</b> my{/} guys', $t);
        $t->tryAppend('foo', ' dear');
        $this->assertSameTemplate('{foo}Hi and <b>welcome</b> my dear{/} guys', $t);
        $t->tryAppend('non_existent_tag', 'ignore this');
        $this->assertSameTemplate('{foo}Hi and <b>welcome</b> my dear{/} guys', $t);
        $t->tryDangerouslyAppendHtml('foo', ' and <b>smart</b>');
        $this->assertSameTemplate('{foo}Hi and <b>welcome</b> my dear and <b>smart</b>{/} guys', $t);
        $t->tryDangerouslyAppendHtml('non_existent_tag', '<b>ignore</b> this');
        $this->assertSameTemplate('{foo}Hi and <b>welcome</b> my dear and <b>smart</b>{/} guys', $t);
    }

    public function testClone(): void
    {
        $t = new HtmlTemplate('{foo}{inner}hello{/}{/} guys');

        $topClone1 = clone $t;
        $this->assertSameTemplate('{foo}{inner}hello{/}{/} guys', $topClone1);
        $topClone2 = $t->cloneRegion('_top');
        $this->assertSameTemplate('{foo}{inner}hello{/}{/} guys', $topClone2);
        $this->assertSameTemplate('{inner}hello{/}', $t->cloneRegion('foo'));
        $this->assertSameTemplate('{inner}hello{/}', $topClone1->cloneRegion('foo'));
        $this->assertSameTemplate('{inner}hello{/}', $topClone2->cloneRegion('foo'));
    }

    public function testRenderRegion(): void
    {
        $t = new HtmlTemplate('{foo}hello{/} guys');
        static::assertSame('hello', $t->renderToHtml('foo'));
    }

    public function testParseDollarTags(): void
    {
        $t = new HtmlTemplate('{$foo} guys and {$bar} here');
        $t->set([
            'foo' => 'Hello',
            'bar' => 'welcome',
        ]);
        $this->assertSameTemplate('{foo}Hello{/} guys and {bar}welcome{/} here', $t);
    }
}
