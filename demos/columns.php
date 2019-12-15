<?php
/**
 * Testing Columns.
 */
require 'init.php';

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

$page = $app->add(['View', 'id' => 'example']);

$page->add(['Header', 'Basic Usage']);

$c = $page->add(new \atk4\ui\Columns());
$c->addColumn()->add(['LoremIpsum', 1]);
$c->addColumn()->add(['LoremIpsum', 1]);
$c->addColumn()->add(['LoremIpsum', 1]);

$page->add(['Header', 'Specifying widths, using rows or automatic flow']);

// highlight class will show cells as boxes, even though they contain nothing
$c = $page->add(new \atk4\ui\Columns([null, 'highlight']));
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

$page->add(['Header', 'Content Outline']);
$c = $page->add(new \atk4\ui\Columns(['internally celled']));

$r = $c->addRow();
$r->addColumn([2, 'right aligned'])->add(['Icon', 'huge home']);
$r->addColumn(12)->add(['LoremIpsum', 1]);
$r->addColumn(2)->add(['Icon', 'huge trash']);

$r = $c->addRow();
$r->addColumn([2, 'right aligned'])->add(['Icon', 'huge home']);
$r->addColumn(12)->add(['LoremIpsum', 1]);
$r->addColumn(2)->add(['Icon', 'huge trash']);

$page->add(['Header', 'Add elements into columns and using classes']);

/**
 * Example box component with some content, good for putting into columns.
 */
class Box extends \atk4\ui\View
{
    public $ui = 'segment';
    public $content = false;

    public function init()
    {
        parent::init();
        $this->add(['Table', 'header' => false])
            ->setSource(['One', 'Two', 'Three', 'Four']);
    }
}

$c = $page->add(new \atk4\ui\Columns(['width' => 4]));
$c->addColumn()->add(new Box([null, 'red']));
$c->addColumn([null, null, 'right floated'])->add(new Box([null, 'blue']));
