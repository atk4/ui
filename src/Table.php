<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Core\Factory;
use Atk4\Data\Field;
use Atk4\Data\Model;
use Atk4\Ui\Js\Jquery;
use Atk4\Ui\Js\JsExpression;
use Atk4\Ui\Js\JsExpressionable;

/**
 * @phpstan-type JsCallbackSetClosure \Closure(Jquery, mixed, mixed, mixed, mixed, mixed, mixed, mixed, mixed, mixed, mixed): (JsExpressionable|View|string|void)
 */
class Table extends Lister
{
    public $ui = 'table';

    public $defaultTemplate = 'table.html';

    /**
     * If table is part of Grid or Crud, we want to reload that instead of table.
     * Usually a Grid or Crud that contains the table.
     *
     * @var View|null
     */
    public $reload;

    /** @var array<int|string, Table\Column|array<int, Table\Column>> Contains list of declared columns. Value will always be a column object. */
    public $columns = [];

    /**
     * Allows you to inject HTML into table using getHtmlTags hook and column callbacks.
     * Switch this feature off to increase performance at expense of some row-specific HTML.
     *
     * @var bool
     */
    public $useHtmlTags = true;

    /**
     * Determines a strategy on how totals will be calculated. Do not touch those fields
     * directly, instead use addTotals().
     *
     * @var array<string, string|array{ string|\Closure(mixed, string, $this): (int|float) }>|false
     */
    public $totalsPlan = false;

    /** @var bool Setting this to false will hide header row. */
    public $header = true;

    /** @var array Contains list of totals accumulated during the render process. */
    public $totals = [];

    /** @var HtmlTemplate|null Contain the template for the "Head" type row. */
    public $tHead;

    /** @var HtmlTemplate */
    public $tRowMaster;

    /** @var HtmlTemplate Contain the template for the "Body" type row. */
    public $tRow;

    /** @var HtmlTemplate Contain the template for the "Foot" type row. */
    public $tTotals;

    /**
     * Set this if you want table to appear as sortable. This does not add any
     * mechanic of actual sorting - either implement manually or use Grid.
     *
     * @var bool|null
     */
    public $sortable;

    /**
     * When $sortable is true, you can specify which column will appear to have
     * active sorting on it.
     *
     * @var string
     */
    public $sortBy;

    /**
     * When $sortable is true, and $sortBy is set, you can set order direction.
     *
     * @var 'asc'|'desc'|null
     */
    public $sortDirection;

    /**
     * Make action columns in table use
     * the collapsing CSS class.
     * An action cell that is collapsing will
     * only uses as much space as required.
     *
     * @var bool
     */
    public $hasCollapsingCssActionColumn = true;

    /**
     * Create one column object that will be used to render all columns
     * in the table unless you have specified a different column object.
     */
    protected function initChunks(): void
    {
        if (!$this->tHead) {
            $this->tHead = $this->template->cloneRegion('Head');
            $this->tRowMaster = $this->template->cloneRegion('Row');
            $this->tTotals = $this->template->cloneRegion('Totals');
            $this->tEmpty = $this->template->cloneRegion('Empty');

            $this->template->del('Head');
            $this->template->del('Body');
            $this->template->del('Foot');
        }
    }

    /**
     * Defines a new column for this field. You need two objects for field to
     * work.
     *
     * First is being Model field. If your Table is already associated with
     * the model, it will automatically pick one by looking up element
     * corresponding to the $name or add it as per your definition inside $field.
     *
     * The other object is a Column Decorator. This object know how to produce HTML for
     * cells and will handle other things, like alignment. If you do not specify
     * column, then it will be selected dynamically based on field type.
     *
     * If you don't want table column to be associated with model field, then
     * pass $name parameter as null.
     *
     * @param string|null                             $name            Data model field name
     * @param array|Table\Column                      $columnDecorator
     * @param ($name is null ? array{} : array|Field) $field
     *
     * @return Table\Column
     */
    public function addColumn(?string $name, $columnDecorator = [], $field = [])
    {
        $this->assertIsInitialized();

        if ($name !== null && isset($this->columns[$name])) {
            throw (new Exception('Table column already exists'))
                ->addMoreInfo('name', $name);
        }

        if (!$this->model) {
            $this->model = new \Atk4\Ui\Misc\ProxyModel();
        }
        $this->model->assertIsModel();

        // should be vaguely consistent with Form\AbstractLayout::addControl()

        if ($name === null) {
            $field = null;
        } elseif (!$this->model->hasField($name)) {
            $field = $this->model->addField($name, $field);
            $field->neverPersist = true;
        } else {
            $field = $this->model->getField($name)
                ->setDefaults($field);
        }

        if ($field === null) {
            // column is not associated with any model field
            // TODO simplify to single $this->decoratorFactory call
            $columnDecorator = $this->_addUnchecked(Table\Column::fromSeed($columnDecorator, ['table' => $this]));
        } else {
            $columnDecorator = $this->decoratorFactory($field, Factory::mergeSeeds($columnDecorator, ['columnData' => $name]));
        }

        if ($name === null) {
            $this->columns[] = $columnDecorator;
        } else {
            $this->columns[$name] = $columnDecorator;
        }

        return $columnDecorator;
    }

    // TODO do not use elements/add(), elements are only for View based objects
    private function _addUnchecked(Table\Column $column): Table\Column
    {
        return \Closure::bind(function () use ($column) {
            return $this->_add($column);
        }, $this, AbstractView::class)();
    }

    /**
     * Set Popup action for columns filtering.
     *
     * @param array $cols an array with columns name that need filtering
     */
    public function setFilterColumn($cols = null): void
    {
        if (!$this->model) {
            throw new Exception('Model need to be defined in order to use column filtering');
        }

        // set filter to all column when null
        if (!$cols) {
            foreach ($this->model->getFields() as $key => $field) {
                if (isset($this->columns[$key])) {
                    $cols[] = $field->shortName;
                }
            }
        }

        // create column popup
        foreach ($cols as $colName) {
            $col = $this->getColumn($colName);

            $pop = $col->addPopup(new Table\Column\FilterPopup(['field' => $this->model->getField($colName), 'reload' => $this->reload, 'colTrigger' => '#' . $col->name . '_ac']));
            if ($pop->isFilterOn()) {
                $col->setHeaderPopupIcon('table-filter-on');
            }
            // apply condition according to popup form
            $this->model = $pop->setFilterCondition($this->model);
        }
    }

    /**
     * Add column Decorator.
     *
     * @param array|Table\Column $seed
     *
     * @return Table\Column
     */
    public function addDecorator(string $name, $seed)
    {
        if (!isset($this->columns[$name])) {
            throw (new Exception('Table column does not exist'))
                ->addMoreInfo('name', $name);
        }

        $decorator = $this->_addUnchecked(Table\Column::fromSeed($seed, ['table' => $this]));

        if (!is_array($this->columns[$name])) {
            $this->columns[$name] = [$this->columns[$name]];
        }
        $this->columns[$name][] = $decorator;

        return $decorator;
    }

    /**
     * Return array of column decorators for particular column.
     */
    public function getColumnDecorators(string $name): array
    {
        $dec = $this->columns[$name];

        return is_array($dec) ? $dec : [$dec];
    }

    /**
     * Return column instance or first instance if using decorator.
     *
     * @return Table\Column
     */
    protected function getColumn(string $name)
    {
        // NOTE: It is not guaranteed that we will have only one element here. When adding decorators, the key will not
        // contain the column instance anymore but an array with column instance set at 0 indexes and the rest as decorators.
        // This is enough for fixing this issue right now. We can work on unifying decorator API in a separate PR.
        return is_array($this->columns[$name]) ? $this->columns[$name][0] : $this->columns[$name];
    }

    /**
     * @var array<string, array>
     */
    protected array $typeToDecorator = [
        'atk4_money' => [Table\Column\Money::class],
        'text' => [Table\Column\Text::class],
        'boolean' => [Table\Column\Status::class, ['positive' => [true], 'negative' => [false]]],
    ];

    /**
     * Will come up with a column object based on the field object supplied.
     * By default will use default column.
     *
     * @param array|Table\Column $seed
     *
     * @return Table\Column
     */
    public function decoratorFactory(Field $field, $seed = [])
    {
        $seed = Factory::mergeSeeds(
            $seed,
            $field->ui['table'] ?? null,
            $this->typeToDecorator[$field->type] ?? null,
            [Table\Column::class]
        );

        return $this->_addUnchecked(Table\Column::fromSeed($seed, ['table' => $this]));
    }

    /**
     * Make columns resizable by dragging column header.
     *
     * The callback function will receive two parameter, a Jquery chain object and a array containing all table columns
     * name and size.
     *
     * @param \Closure(Jquery, mixed): (JsExpressionable|View|string|void) $fx             a callback function with columns widths as parameter
     * @param array<int, int>                                              $widths         ex: [100, 200, 300, 100]
     * @param array                                                        $resizerOptions column-resizer module options, see https://www.npmjs.com/package/column-resizer
     *
     * @return $this
     */
    public function resizableColumn($fx = null, $widths = null, $resizerOptions = [])
    {
        $options = [];
        if ($fx !== null) {
            $cb = JsCallback::addTo($this);
            $cb->set(function (Jquery $chain, string $data) use ($fx) {
                return $fx($chain, $this->getApp()->decodeJson($data));
            }, ['widths' => 'widths']);
            $options['url'] = $cb->getJsUrl();
        }

        if ($widths !== null) {
            $options['widths'] = $widths;
        }

        $options = array_merge($options, $resizerOptions);

        $this->js(true, $this->js()->atkColumnResizer($options));

        return $this;
    }

    /**
     * Add a dynamic paginator, i.e. when user is scrolling content.
     *
     * @param int    $ipp          number of item per page to start with
     * @param array  $options      an array with JS Scroll plugin options
     * @param View   $container    the container holding the lister for scrolling purpose
     * @param string $scrollRegion A specific template region to render. Render output is append to container HTML element.
     *
     * @return $this
     */
    public function addJsPaginator($ipp, $options = [], $container = null, $scrollRegion = 'Body')
    {
        $options = array_merge($options, ['appendTo' => 'tbody']);

        return parent::addJsPaginator($ipp, $options, $container, $scrollRegion);
    }

    /**
     * Override works like this:.
     * [
     *   'name' => 'Totals for {$num} rows:',
     *   'price' => '--',
     *   'total' => ['sum']
     * ].
     *
     * @param array<string, string|array{ string|\Closure(mixed, string, $this): (int|float) }> $plan
     */
    public function addTotals($plan = []): void
    {
        $this->totalsPlan = $plan;
    }

    /**
     * Sets data Model of Table.
     *
     * If $columns is not defined, then automatically will add columns for all
     * visible model fields. If $columns is set to false, then will not add
     * columns at all.
     *
     * @param array<int, string>|null $columns
     */
    public function setModel(Model $model, array $columns = null): void
    {
        $model->assertIsModel();

        parent::setModel($model);

        if ($columns === null) {
            $columns = array_keys($model->getFields('visible'));
        }

        foreach ($columns as $column) {
            $this->addColumn($column);
        }
    }

    protected function renderView(): void
    {
        if (!$this->columns) {
            throw (new Exception('Table does not have any columns defined'))
                ->addMoreInfo('columns', $this->columns);
        }

        if ($this->sortable) {
            $this->addClass('sortable');
        }

        // generate Header Row
        if ($this->header) {
            $this->tHead->dangerouslySetHtml('cells', $this->getHeaderRowHtml());
            $this->template->dangerouslySetHtml('Head', $this->tHead->renderToHtml());
        }

        // generate template for data row
        $this->tRowMaster->dangerouslySetHtml('cells', $this->getDataRowHtml());
        $this->tRowMaster->set('dataId', '{$dataId}');
        $this->tRow = new HtmlTemplate($this->tRowMaster->renderToHtml());
        $this->tRow->setApp($this->getApp());

        // iterate data rows
        $this->_renderedRowsCount = 0;

        // TODO we should not iterate using $this->model variable,
        // then also backup/tryfinally would be not needed
        // the same in Lister class
        $modelBackup = $this->model;
        $tRowBackup = $this->tRow;
        try {
            foreach ($this->model as $this->model) {
                $this->currentRow = $this->model;
                $this->tRow = clone $tRowBackup;
                if ($this->hook(self::HOOK_BEFORE_ROW) === false) {
                    continue;
                }

                if ($this->totalsPlan) {
                    $this->updateTotals();
                }

                $this->renderRow();

                ++$this->_renderedRowsCount;

                if ($this->hook(self::HOOK_AFTER_ROW) === false) {
                    continue;
                }
            }
        } finally {
            $this->model = $modelBackup;
            $this->tRow = $tRowBackup;
        }

        // add totals rows or empty message
        if ($this->_renderedRowsCount === 0) {
            if (!$this->jsPaginator || !$this->jsPaginator->getPage()) {
                $this->template->dangerouslyAppendHtml('Body', $this->tEmpty->renderToHtml());
            }
        } elseif ($this->totalsPlan) {
            $this->tTotals->dangerouslySetHtml('cells', $this->getTotalsRowHtml());
            $this->template->dangerouslyAppendHtml('Foot', $this->tTotals->renderToHtml());
        }

        // stop JsPaginator if there are no more records to fetch
        if ($this->jsPaginator && ($this->_renderedRowsCount < $this->ipp)) {
            $this->jsPaginator->jsIdle();
        }

        View::renderView();
    }

    /**
     * Render individual row. Override this method if you want to do more
     * decoration.
     */
    public function renderRow(): void
    {
        $this->tRow->set($this->model);

        if ($this->useHtmlTags) {
            // prepare row-specific HTML tags
            $htmlTags = [];

            foreach ($this->hook(Table\Column::HOOK_GET_HTML_TAGS, [$this->model]) as $ret) {
                if (is_array($ret)) {
                    $htmlTags = array_merge($htmlTags, $ret);
                }
            }

            foreach ($this->columns as $name => $columns) {
                if (!is_array($columns)) {
                    $columns = [$columns];
                }
                $field = is_int($name) ? null : $this->model->getField($name);
                foreach ($columns as $column) {
                    $htmlTags = array_merge($column->getHtmlTags($this->model, $field), $htmlTags);
                }
            }

            // render row and add to body
            $this->tRow->dangerouslySetHtml($htmlTags);
            $this->tRow->set('dataId', (string) $this->model->getId());
            $this->template->dangerouslyAppendHtml('Body', $this->tRow->renderToHtml());
            $this->tRow->del(array_keys($htmlTags));
        } else {
            $this->template->dangerouslyAppendHtml('Body', $this->tRow->renderToHtml());
        }
    }

    /**
     * Same as on('click', 'tr', $action), but will also make sure you can't
     * click outside of the body. Additionally when you move cursor over the
     * rows, pointer will be used and rows will be highlighted as you hover.
     *
     * @param JsExpressionable|JsCallbackSetClosure $action Code to execute
     */
    public function onRowClick($action): void
    {
        $this->addClass('selectable');
        $this->js(true)->find('tbody')->css('cursor', 'pointer');

        // do not bubble row click event if click stems from row content like checkboxes
        // TODO one ->on() call would be better, but we need a method to convert Closure $action into JsExpression first
        $preventBubblingJs = new JsExpression(<<<'EOF'
            let elem = event.target;
            while (elem !== null && elem !== event.currentTarget) {
                if (elem.tagName === 'A' || elem.classList.contains('atk4-norowclick')
                    || (elem.classList.contains('ui') && ['button', 'input', 'checkbox', 'dropdown'].some(v => elem.classList.contains(v)))) {
                    event.stopImmediatePropagation();
                }
                elem = elem.parentElement;
            }
            EOF);
        $this->on('click', 'tbody > tr', $preventBubblingJs, ['preventDefault' => false]);

        $this->on('click', 'tbody > tr', $action);
    }

    /**
     * Use this to quickly access the <tr> and wrap in Jquery.
     *
     * $this->jsRow()->data('id');
     *
     * @return Jquery
     */
    public function jsRow(): JsExpressionable
    {
        return (new Jquery())->closest('tr');
    }

    /**
     * Remove a row in table using javascript using a model ID.
     *
     * @param string $id         the model ID where row need to be removed
     * @param string $transition the transition effect
     *
     * @return Jquery
     */
    public function jsRemoveRow($id, $transition = 'fade left'): JsExpressionable
    {
        return $this->js()->find('tr[data-id=' . $id . ']')->transition($transition);
    }

    /**
     * Executed for each row if "totals" are enabled to add up values.
     */
    public function updateTotals(): void
    {
        foreach ($this->totalsPlan as $key => $val) {
            // if value is array, then we treat it as built-in or closure aggregate method
            if (is_array($val)) {
                $f = $val[0];

                // initial value is always 0
                if (!isset($this->totals[$key])) {
                    $this->totals[$key] = 0;
                }

                if ($f instanceof \Closure) {
                    $this->totals[$key] += $f($this->model->get($key), $key, $this);
                } elseif (is_string($f)) {
                    switch ($f) {
                        case 'sum':
                            $this->totals[$key] += $this->model->get($key);

                            break;
                        case 'count':
                            ++$this->totals[$key];

                            break;
                        case 'min':
                            if ($this->model->get($key) < $this->totals[$key]) {
                                $this->totals[$key] = $this->model->get($key);
                            }

                            break;
                        case 'max':
                            if ($this->model->get($key) > $this->totals[$key]) {
                                $this->totals[$key] = $this->model->get($key);
                            }

                            break;
                        default:
                            throw (new Exception('Unsupported table aggregate function'))
                                ->addMoreInfo('name', $f);
                    }
                }
            }
        }
    }

    /**
     * Responds with the HTML to be inserted in the header row that would
     * contain captions of all columns.
     */
    public function getHeaderRowHtml(): string
    {
        $output = [];
        foreach ($this->columns as $name => $column) {
            // if multiple formatters are defined, use the first for the header cell
            if (is_array($column)) {
                $column = $column[0];
            }

            if (!is_int($name)) {
                $field = $this->model->getField($name);

                $output[] = $column->getHeaderCellHtml($field);
            } else {
                $output[] = $column->getHeaderCellHtml();
            }
        }

        return implode('', $output);
    }

    /**
     * Responds with HTML to be inserted in the footer row that would
     * contain totals for all columns.
     */
    public function getTotalsRowHtml(): string
    {
        $output = [];
        foreach ($this->columns as $name => $column) {
            // if no totals plan, then show dash, but keep column formatting
            if (!isset($this->totalsPlan[$name])) {
                $output[] = $column->getTag('foot', '-');

                continue;
            }

            // if totals plan is set as array, then show formatted value
            if (is_array($this->totalsPlan[$name])) {
                $field = $this->model->getField($name);
                $output[] = $column->getTotalsCellHtml($field, $this->totals[$name]);

                continue;
            }

            // otherwise just show it, for example, "Totals:" cell
            $output[] = $column->getTag('foot', $this->totalsPlan[$name]);
        }

        return implode('', $output);
    }

    /**
     * Collects cell templates from all the columns and combine them into row template.
     */
    public function getDataRowHtml(): string
    {
        $output = [];
        foreach ($this->columns as $name => $column) {
            // if multiple formatters are defined, use the first for the header cell
            $field = !is_int($name) ? $this->model->getField($name) : null;

            if (!is_array($column)) {
                $column = [$column];
            }

            // we need to smartly wrap things up
            $cell = null;
            $tdAttr = [];
            foreach ($column as $cKey => $c) {
                if ($cKey !== array_key_last($column)) {
                    $html = $c->getDataCellTemplate($field);
                    $tdAttr = $c->getTagAttributes('body', $tdAttr);
                } else {
                    // last formatter, ask it to give us whole rendering
                    $html = $c->getDataCellHtml($field, $tdAttr);
                }

                if ($cell) {
                    if ($name) {
                        // if name is set, we can wrap things
                        $cell = str_replace('{$' . $name . '}', $cell, $html);
                    } else {
                        $cell .= ' ' . $html;
                    }
                } else {
                    $cell = $html;
                }
            }

            $output[] = $cell;
        }

        return implode('', $output);
    }
}
