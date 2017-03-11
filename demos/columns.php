<?php
/**
 * Testing Columns
 */
require 'init.php';

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

$page = $layout->add(['View', 'id'=>'example']);


$page->add(['Header', 'Basic Column']);

// highlight class will show cells as boxes, even though they contain nothing
$c = $page->add(new \atk4\ui\Columns([null, 'highlight']));
$c->addColumn(3);
$c->addColumn(5);
$c->addColumn(2);
$c->addColumn(6);

$r = $c->addRow();
$r->addColumn();
$r->addColumn();
$r->addColumn();

$page->add(['Header', 'Add elements into columns']);

/**
 * Example box with some content, good for putting into columns
 */
class Box extends \atk4\ui\View
{
    public $ui = 'segment';
    public $content = false;
    public function init() {
        parent::init();
        $this->add('Table')->setSource(['One', 'Two', 'Three', 'Four']);
    }
}

$c = $page->add(new \atk4\ui\Columns(['width'=>4]));
$c->addColumn()->add(new Box(['red']));
$c->addColumn([null, 'right floated'])->add(new Box(['blue']));
