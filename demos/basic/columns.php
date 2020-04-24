<?php
/**
 * Testing Columns.
 */
chdir('..');

require_once 'atk-init.php';




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

$page = \atk4\ui\View::addTo($app, ['id' => 'example']);

\atk4\ui\Header::addTo($page, ['Basic Usage']);

$c = \atk4\ui\Columns::addTo($page);
\atk4\ui\LoremIpsum::addTo($c->addColumn(), [1]);
\atk4\ui\LoremIpsum::addTo($c->addColumn(), [1]);
\atk4\ui\LoremIpsum::addTo($c->addColumn(), [1]);

\atk4\ui\Header::addTo($page, ['Specifying widths, using rows or automatic flow']);

// highlight class will show cells as boxes, even though they contain nothing
$c = \atk4\ui\Columns::addTo($page, [null, 'highlight']);
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

\atk4\ui\Header::addTo($page, ['Content Outline']);
$c = \atk4\ui\Columns::addTo($page, ['internally celled']);

$r = $c->addRow();
\atk4\ui\Icon::addTo($r->addColumn([2, 'right aligned']), ['huge home']);
\atk4\ui\LoremIpsum::addTo($r->addColumn(12), [1]);
\atk4\ui\Icon::addTo($r->addColumn(2), ['huge trash']);

$r = $c->addRow();
\atk4\ui\Icon::addTo($r->addColumn([2, 'right aligned']), ['huge home']);
\atk4\ui\LoremIpsum::addTo($r->addColumn(12), [1]);
\atk4\ui\Icon::addTo($r->addColumn(2), ['huge trash']);

\atk4\ui\Header::addTo($page, ['Add elements into columns and using classes']);

/**
 * Example box component with some content, good for putting into columns.
 */
class Box extends \atk4\ui\View
{
    public $ui = 'segment';
    public $content = false;

    public function init(): void
    {
        parent::init();
        \atk4\ui\Table::addTo($this, ['header' => false])
            ->setSource(['One', 'Two', 'Three', 'Four']);
    }
}

$c = \atk4\ui\Columns::addTo($page, ['width' => 4]);
Box::addTo($c->addColumn(), [null, 'red']);
Box::addTo($c->addColumn([null, null, 'right floated']), [null, 'blue']);
