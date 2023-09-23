<?php

declare(strict_types=1);

namespace Atk4\Ui;

class GridLayout extends View
{
    public $ui = 'grid';

    public $defaultTemplate = 'grid-layout.html';

    /** @var int Number of rows */
    protected $rows = 1;

    /** @var int<1, 16> Number of columns */
    protected $columns = 2;

    /** @var array<int, string> */
    protected $cssWideClasses = [
        1 => 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine',
        'ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen',
    ];

    /** @var HtmlTemplate */
    protected $tWrap;
    /** @var HtmlTemplate */
    protected $tRow;
    /** @var HtmlTemplate */
    protected $tCol;

    /** @var string CSS class for columns view */
    public $columnClass = '';

    protected function init(): void
    {
        parent::init();

        $this->template->set('columnClass', $this->columnClass);

        // extract template parts
        $this->tWrap = clone $this->template;
        $this->tRow = $this->template->cloneRegion('row');
        $this->tCol = $this->template->cloneRegion('column');

        // clean them
        $this->tRow->del('column');
        $this->tWrap->del('rows');

        // will need to manipulate template a little
        $this->buildTemplate();
    }

    protected function buildTemplate(): void
    {
        $this->tWrap->del('rows');
        $this->tWrap->dangerouslyAppendHtml('rows', '{rows}');

        for ($row = 1; $row <= $this->rows; ++$row) {
            $this->tRow->del('column');

            for ($col = 1; $col <= $this->columns; ++$col) {
                $this->tCol->set('Content', '{$r' . $row . 'c' . $col . '}');

                $this->tRow->dangerouslyAppendHtml('column', $this->tCol->renderToHtml());
            }

            $this->tWrap->dangerouslyAppendHtml('rows', $this->tRow->renderToHtml());
        }
        $this->tWrap->dangerouslyAppendHtml('rows', '{/rows}');
        $tmp = new HtmlTemplate($this->tWrap->renderToHtml());

        // TODO replace later, the only use of direct template tree manipulation
        $t = $this->template;
        \Closure::bind(static function () use ($t, $tmp) {
            $cloneTagTreeFx = static function (HtmlTemplate\TagTree $src) use (&$cloneTagTreeFx, $t) {
                $tagTree = $src->clone($t);
                $t->tagTrees[$src->getTag()] = $tagTree;
                \Closure::bind(static function () use ($tagTree, $cloneTagTreeFx, $src) {
                    foreach ($tagTree->children as $v) {
                        if (is_string($v)) {
                            $cloneTagTreeFx($src->getParentTemplate()->getTagTree($v));
                        }
                    }
                }, null, HtmlTemplate\TagTree::class)();
            };
            $cloneTagTreeFx($tmp->getTagTree('rows'));

            // TODO prune unreachable nodes
            // $template->rebuildTagsIndex();
        }, null, HtmlTemplate::class)();

        $this->addClass($this->cssWideClasses[$this->columns] . ' column');
    }
}
