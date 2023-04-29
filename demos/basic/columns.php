<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Columns;
use Atk4\Ui\Header;
use Atk4\Ui\Icon;
use Atk4\Ui\LoremIpsum;
use Atk4\Ui\Table;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

// some custom style needed for our "highlight" to work. You don't need this on
// your page and it's bad style to include CSS like this!
$app->addStyle('
#example .highlight.grid .column:not(.row):not(.grid):after {
    background-color: rgba(86, 61, 124, .1);
    -webkit-box-shadow: 0px 0px 0px 1px rgba(86, 61, 124, 0.2) inset;
    box-shadow: 0px 0px 0px 1px rgba(86, 61, 124, 0.2) inset;
    content: "";
    display: block;
    min-height: 50px;
}
');

$page = View::addTo($app, ['name' => 'example']);

Header::addTo($page, ['Basic Usage']);

$c = Columns::addTo($page);
LoremIpsum::addTo($c->addColumn(), [1]);
LoremIpsum::addTo($c->addColumn(), [1]);
LoremIpsum::addTo($c->addColumn(), [1]);

Header::addTo($page, ['Specifying widths, using rows or automatic flow']);

// highlight class will show cells as boxes, even though they contain nothing
$c = Columns::addTo($page, ['class.highlight' => true]);
$c->addColumn(3);
$c->addColumn(5);
$c->addColumn(2);
$c->addColumn(6);
$c->addColumn(5);
$c->addColumn(2);
$c->addColumn(6);
$c->addColumn(3);

$r = $c->addRow();
$r->addColumn();
$r->addColumn();
$r->addColumn();

Header::addTo($page, ['Content Outline']);
$c = Columns::addTo($page, ['internally celled']);

$r = $c->addRow();
Icon::addTo($r->addColumn([2, 'class.right aligned' => true]), ['huge home']);
LoremIpsum::addTo($r->addColumn(12), [1]);
Icon::addTo($r->addColumn(2), ['huge trash']);

$r = $c->addRow();
Icon::addTo($r->addColumn([2, 'class.right aligned' => true]), ['huge home']);
LoremIpsum::addTo($r->addColumn(12), [1]);
Icon::addTo($r->addColumn(2), ['huge trash']);

Header::addTo($page, ['Add elements into columns and using classes']);

// Example box component with some content, good for putting into columns.

$boxClass = AnonymousClassNameCache::get_class(fn () => new class() extends View {
    public $ui = 'segment';

    protected function init(): void
    {
        parent::init();

        Table::addTo($this, ['header' => false])
            ->setSource(['One', 'Two', 'Three', 'Four']);
    }
});

$c = Columns::addTo($page, ['width' => 4]);
$boxClass::addTo($c->addColumn(), ['class.red' => true]);
$boxClass::addTo($c->addColumn(['class.right floated' => true]), ['class.blue' => true]);
