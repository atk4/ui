<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Core\Factory;

class Table extends Lister
{
    // Overrides
    public $defaultTemplate = 'table.html';
    public $ui = 'table';
    public $content = false;

    /**
     * If table is part of Grid or Crud, we want to reload that instead of table.
     *
     * @var View|null ususally a Grid or Crud view that contains the table
     */
    public $reload;

    /**
     * Column objects can service multiple columns. You can use it for your advantage by re-using the object
     * when you pass it to addColumn(). If you omit the argument, then a column of a type Table\Column
     * will be used.
     *
     * @var Table\Column
     */
    public $default_column;

    /**
     * Contains list of declared columns. Value will always be a column object.
     *
     * @var array
     */
    public $columns = [];

    /**
     * Allows you to inject HTML into table using getHtmlTags hook and column call-backs.
     * Switch this feature off to increase performance at expense of some row-specific HTML.
     *
     * @var bool
     */
    public $use_html_tags = true;

    /**
     * Determines a strategy on how totals will be calculated. Do not touch those fields
     * direcly, instead use addTotals().
     *
     * @var bool
     */
    public $totals_plan = false;

    /**
     * Setting this to false will hide header row.
     *
     * @var bool
     */
    public $header = true;

    /**
     * Contains list of totals accumulated during the render process.
     *
     * @var array
     */
    public $totals = [];

    /**
     * Contain the template for the "Head" type row.
     *
     * @var HtmlTemplate
     */
    public $t_head;

    /**
     * Contain the template for the "Body" type row.
     *
     * @var HtmlTemplate
     */
    public $t_row;

    /**
     * Contain the template for the "Foot" type row.
     *
     * @var HtmlTemplate
     */
    public $t_totals;

    /**
     * Contains the output to show if table contains no rows.
     *
     * @var HtmlTemplate
     */
    public $t_empty;

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
    public $sort_by;

    /**
     * When $sortable is true, and $sort_by is set, you can set this to
     * "ascending" or "descending".
     *
     * @var string
     */
    public $sort_order;

    /**
     * Make action columns in table use
     * the collapsing css class.
     * An action cell that is collapsing will
     * only uses as much space as required.
     *
     * @var bool
     */
    public $hasCollapsingCssActionColumn = true;

    /**
     * Constructor.
     *
     * @param string|null $class CSS class to add
     */
    public function __construct($class = null)
    {
        if ($class) {
            $this->addClass($class);
        }
    }

    /**
     * initChunks method will create one column object that will be used to render
     * all columns in the table unless you have specified a different
     * column object.
     */
    public function initChunks()
    {
        if (!$this->t_head) {
            $this->t_head = $this->template->cloneRegion('Head');
            $this->t_row_master = $this->template->cloneRegion('Row');
            $this->t_totals = $this->template->cloneRegion('Totals');
            $this->t_empty = $this->template->cloneRegion('Empty');

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
     * @param string|null              $name            Data model field name
     * @param array|string|object|null $columnDecorator
     * @param array|string|object|null $field
     *
     * @return Table\Column
     */
    public function addColumn(?string $name, $columnDecorator = null, $field = null)
    {
        if (!$this->_initialized) {
            throw new Exception\NoRenderTree($this, 'addColumn()');
        }

        if (!$this->model) {
            $this->model = new \Atk4\Ui\Misc\ProxyModel();
        }

        // This code should be vaugely consistent with Form\Layout::addControl()

        if (is_string($field)) {
            $field = ['type' => $field];
        }

        if ($name === null) {
            // table column without respective field in model
            $field = null;
        } elseif (!$this->model->hasField($name)) {
            $field = $this->model->addField($name, $field);

            $field->never_persist = true;
        } else {
            $existingField = $this->model->getField($name);

            if (is_array($field)) {
                $field = $existingField->setDefaults($field);
            } elseif (is_object($field)) {
                throw (new Exception('Duplicate field'))
                    ->addMoreInfo('name', $name);
            } else {
                $field = $existingField;
            }
        }

        if ($field === null) {
            // column is not associated with any model field
            $columnDecorator = $this->_addUnchecked(Table\Column::fromSeed($columnDecorator, ['table' => $this]));
        } elseif (is_array($columnDecorator) || is_string($columnDecorator)) {
            $columnDecorator = $this->decoratorFactory($field, array_merge(['columnData' => $name], is_string($columnDecorator) ? [$columnDecorator] : $columnDecorator));
        } elseif (!$columnDecorator) {
            $columnDecorator = $this->decoratorFactory($field, ['columnData' => $name]);
        } elseif (is_object($columnDecorator)) {
            if (!$columnDecorator instanceof Table\Column) {
                throw (new Exception('Column decorator must descend from ' . Table\Column::class))
                    ->addMoreInfo('columnDecorator', $columnDecorator);
            }
            $columnDecorator->table = $this;
            if (!$columnDecorator->columnData) {
                $columnDecorator->columnData = $name;
            }
            $this->_addUnchecked($columnDecorator);
        } else {
            throw (new Exception('Value of $columnDecorator argument is incorrect'))
                ->addMoreInfo('columnDecorator', $columnDecorator);
        }

        if ($name === null) {
            $this->columns[] = $columnDecorator;
        } elseif (!is_string($name)) {
            throw (new Exception('Name must be a string'))
                ->addMoreInfo('name', $name);
        } elseif (isset($this->columns[$name])) {
            throw (new Exception('Table already has column with $name. Try using addDecorator()'))
                ->addMoreInfo('name', $name);
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
     * @param array $cols an array with colomns name that need filtering
     */
    public function setFilterColumn($cols = null)
    {
        if (!$this->model) {
            throw new Exception('Model need to be defined in order to use column filtering.');
        }

        // set filter to all column when null.
        if (!$cols) {
            foreach ($this->model->getFields() as $key => $field) {
                if (!empty($this->columns[$key])) {
                    $cols[] = $field->short_name;
                }
            }
        }

        // create column popup.
        foreach ($cols as $colName) {
            $col = $this->getColumn($colName);
            if ($col) {
                $pop = $col->addPopup(new Table\Column\FilterPopup(['field' => $this->model->getField($colName), 'reload' => $this->reload, 'colTrigger' => '#' . $col->name . '_ac']));
                $pop->isFilterOn() ? $col->setHeaderPopupIcon('table-filter-on') : null;
                // apply condition according to popup form.
                $this->model = $pop->setFilterCondition($this->model);
            }
        }
    }

    /**
     * Add column Decorator.
     *
     * @param string $name Column name
     * @param mixed  $seed Defaults to pass to Factory::factory() when decorator is initialized
     *
     * @return Table\Column
     */
    public function addDecorator(string $name, $seed)
    {
        if (!$this->columns[$name]) {
            throw (new Exception('No such column, cannot decorate'))
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
     *
     * @param string $name Column name
     */
    public function getColumnDecorators(string $name): array
    {
        $dec = $this->columns[$name];

        return is_array($dec) ? $dec : [$dec];
    }

    /**
     * Return column instance or first instance if using decorator.
     *
     * @return mixed
     */
    protected function getColumn(string $name)
    {
        // NOTE: It is not guaranteed that we will have only one element here. When adding decorators, the key will not
        // contain the column instance anymore but an array with column instance set at 0 indexes and the rest as decorators.
        // This is enough for fixing this issue right now. We can work on unifying decorator API in a separate PR.
        return is_array($this->columns[$name]) ? $this->columns[$name][0] : $this->columns[$name];
    }

    /**
     * Will come up with a column object based on the field object supplied.
     * By default will use default column.
     *
     * @param \Atk4\Data\Field $field Data model field
     * @param mixed            $seed  Defaults to pass to Factory::factory() when decorator is initialized
     *
     * @return Table\Column
     */
    public function decoratorFactory(\Atk4\Data\Field $field, $seed = [])
    {
        $seed = Factory::mergeSeeds(
            $seed,
            $field->ui['table'] ?? null,
            $this->typeToDecorator[$field->type] ?? null,
            [$this->default_column ? $this->default_column : Table\Column::class]
        );

        return $this->_addUnchecked(Table\Column::fromSeed($seed, ['table' => $this]));
    }

    protected $typeToDecorator = [
        'password' => [Table\Column\Password::class],
        'money' => [Table\Column\Money::class],
        'text' => [Table\Column\Text::class],
        'boolean' => [Table\Column\Status::class, ['positive' => [true], 'negative' => [false]]],
    ];

    /**
     * Make columns resizable by dragging column header.
     *
     * The callback param function will receive two parameter, a jQuery chain object and a json string containing all table columns
     * name and size. To retrieve columns width, simply json decode the $widths param in your callback function.
     * ex:
     *  $table->resizableColumn(function($j, $w){
     *       // do somethings with columns width
     *       $columns = $this->getApp()->decodeJson($w);
     *   });
     *
     * @param \Closure $fx             a callback function with columns widths as parameter
     * @param int[]    $widths         An array of widths value, integer only. ex: [100,200,300,100]
     * @param array    $resizerOptions An array of column-resizer module options. see https://www.npmjs.com/package/column-resizer
     *
     * @return $this
     */
    public function resizableColumn($fx = null, $widths = null, $resizerOptions = null)
    {
        $options = [];
        if ($fx instanceof \Closure) {
            $cb = JsCallback::addTo($this);
            $cb->set($fx, ['widths' => 'widths']);
            $options['uri'] = $cb->getJsUrl();
        } elseif (is_array($fx)) {
            $widths = $fx;
        }

        if ($widths) {
            $options['widths'] = $widths;
        }

        if ($resizerOptions) {
            $options = array_merge($options, $resizerOptions);
        }

        $this->js(true, $this->js()->atkColumnResizer($options));

        return $this;
    }

    /**
     * Add a dynamic paginator, i.e. when user is scrolling content.
     *
     * @param int    $ipp          number of item per page to start with
     * @param array  $options      an array with js Scroll plugin options
     * @param View   $container    The container holding the lister for scrolling purpose. Default to view owner.
     * @param string $scrollRegion A specific template region to render. Render output is append to container html element.
     *
     * @return $this|void
     */
    public function addJsPaginator($ipp, $options = [], $container = null, $scrollRegion = 'Body')
    {
        $options = array_merge($options, ['appendTo' => 'tbody']);

        return parent::addJsPaginator($ipp, $options, $container, $scrollRegion);
    }

    /**
     * Override works like this:.
     * [
     *   'name'=>'Totals for {$num} rows:',
     *   'price'=>'--',
     *   'total'=>['sum']
     * ].
     *
     * @param array $plan
     */
    public function addTotals($plan = [])
    {
        $this->totals_plan = $plan;
    }

    /**
     * Sets data Model of Table.
     *
     * If $columns is not defined, then automatically will add columns for all
     * visible model fields. If $columns is set to false, then will not add
     * columns at all.
     *
     * @param array|bool $columns
     *
     * @return \Atk4\Data\Model
     */
    public function setModel(\Atk4\Data\Model $model, $columns = null)
    {
        parent::setModel($model);

        if ($columns === null) {
            $columns = array_keys($model->getFields('visible'));
        } elseif ($columns === false) {
            return $this->model;
        }

        foreach ($columns as $column) {
            $this->addColumn($column);
        }

        return $this->model;
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

        // Generate Header Row
        if ($this->header) {
            $this->t_head->dangerouslySetHtml('cells', $this->getHeaderRowHtml());
            $this->template->dangerouslySetHtml('Head', $this->t_head->renderToHtml());
        }

        // Generate template for data row
        $this->t_row_master->dangerouslySetHtml('cells', $this->getDataRowHtml());
        $this->t_row_master->set('_id', '{$_id}');
        $this->t_row = new HtmlTemplate($this->t_row_master->renderToHtml());
        $this->t_row->setApp($this->getApp());

        // Iterate data rows
        $this->_rendered_rows_count = 0;

        // TODO we should not iterate using $this->model variable,
        // then also backup/tryfinally would be not needed
        // the same in Lister class
        $modelBackup = $this->model;
        try {
            foreach ($this->model as $this->model) {
                $this->current_row = $this->model;
                if ($this->hook(self::HOOK_BEFORE_ROW) === false) {
                    continue;
                }

                if ($this->totals_plan) {
                    $this->updateTotals();
                }

                $this->renderRow();

                ++$this->_rendered_rows_count;

                if ($this->hook(self::HOOK_AFTER_ROW) === false) {
                    continue;
                }
            }
        } finally {
            $this->model = $modelBackup;
        }

        // Add totals rows or empty message
        if (!$this->_rendered_rows_count) {
            if (!$this->jsPaginator || !$this->jsPaginator->getPage()) {
                $this->template->dangerouslyAppendHtml('Body', $this->t_empty->renderToHtml());
            }
        } elseif ($this->totals_plan) {
            $this->t_totals->dangerouslySetHtml('cells', $this->getTotalsRowHtml());
            $this->template->dangerouslyAppendHtml('Foot', $this->t_totals->renderToHtml());
        }

        // stop JsPaginator if there are no more records to fetch
        if ($this->jsPaginator && ($this->_rendered_rows_count < $this->ipp)) {
            $this->jsPaginator->jsIdle();
        }

        View::renderView();
    }

    /**
     * Render individual row. Override this method if you want to do more
     * decoration.
     */
    public function renderRow()
    {
        $this->t_row->set($this->model);

        if ($this->use_html_tags) {
            // Prepare row-specific HTML tags.
            $html_tags = [];

            foreach ($this->hook(Table\Column::HOOK_GET_HTML_TAGS, [$this->model]) as $ret) {
                if (is_array($ret)) {
                    $html_tags = array_merge($html_tags, $ret);
                }
            }

            foreach ($this->columns as $name => $columns) {
                if (!is_array($columns)) {
                    $columns = [$columns];
                }
                $field = !is_int($name) && $this->model->hasField($name) ? $this->model->getField($name) : null;
                foreach ($columns as $column) {
                    if (!method_exists($column, 'getHtmlTags')) {
                        continue;
                    }
                    $html_tags = array_merge($column->getHtmlTags($this->model, $field), $html_tags);
                }
            }

            // Render row and add to body
            $this->t_row->dangerouslySetHtml($html_tags);
            $this->t_row->set('_id', $this->model->getId());
            $this->template->dangerouslyAppendHtml('Body', $this->t_row->renderToHtml());
            $this->t_row->del(array_keys($html_tags));
        } else {
            $this->template->dangerouslyAppendHtml('Body', $this->t_row->renderToHtml());
        }
    }

    /**
     * Same as on('click', 'tr', $action), but will also make sure you can't
     * click outside of the body. Additionally when you move cursor over the
     * rows, pointer will be used and rows will be highlighted as you hover.
     *
     * @param JsChain|\Closure|JsExpressionable $action Code to execute
     *
     * @return Jquery
     */
    public function onRowClick($action)
    {
        $this->addClass('selectable');
        $this->js(true)->find('tbody')->css('cursor', 'pointer');

        return $this->on('click', 'tbody>tr', $action);
    }

    /**
     * Use this to quickly access the <tr> and wrap in Jquery.
     *
     * $this->jsRow()->data('id');
     *
     * @return Jquery
     */
    public function jsRow()
    {
        return (new Jquery(new JsExpression('this')))->closest('tr');
    }

    /**
     * Remove a row in table using javascript using a model id.
     *
     * @param string $id         the model id where row need to be removed
     * @param string $transition the transition effect
     *
     * @return mixed
     */
    public function jsRemoveRow($id, $transition = 'fade left')
    {
        return $this->js()->find("tr[data-id={$id}]")->transition($transition);
    }

    /**
     * Executed for each row if "totals" are enabled to add up values.
     */
    public function updateTotals()
    {
        foreach ($this->totals_plan as $key => $val) {
            // if value is array, then we treat it as built-in or closure aggregate method
            if (is_array($val)) {
                $f = $val[0]; // shortcut

                // initial value is always 0
                if (!isset($this->totals[$key])) {
                    $this->totals[$key] = 0;
                }

                // closure support
                // arguments - current value, key, \Atk4\Ui\Table object
                if ($f instanceof \Closure) {
                    $this->totals[$key] += ($f($this->model->get($key), $key, $this) ?: 0);
                } elseif (is_string($f)) { // built-in methods
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
                            if ($this->model¨->get($key) > $this->totals[$key]) {
                                $this->totals[$key] = $this->model->get($key);
                            }

                            break;
                        default:
                            throw (new Exception('Aggregation method does not exist'))
                                ->addMoreInfo('method', $f);
                    }
                }
            }
        }
    }

    /**
     * Responds with the HTML to be inserted in the header row that would
     * contain captions of all columns.
     *
     * @return string
     */
    public function getHeaderRowHtml()
    {
        $output = [];
        foreach ($this->columns as $name => $column) {
            // If multiple formatters are defined, use the first for the header cell
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
     *
     * @return string
     */
    public function getTotalsRowHtml()
    {
        $output = [];
        foreach ($this->columns as $name => $column) {
            // if no totals plan, then show dash, but keep column formatting
            if (!isset($this->totals_plan[$name])) {
                $output[] = $column->getTag('foot', '-');

                continue;
            }

            // if totals plan is set as array, then show formatted value
            if (is_array($this->totals_plan[$name])) {
                // todo - format
                $field = $this->model->getField($name);
                $output[] = $column->getTotalsCellHtml($field, $this->totals[$name]);

                continue;
            }

            // otherwise just show it, for example, "Totals:" cell
            $output[] = $column->getTag('foot', $this->totals_plan[$name]);
        }

        return implode('', $output);
    }

    /**
     * Collects cell templates from all the columns and combine them into row template.
     *
     * @return string
     */
    public function getDataRowHtml()
    {
        $output = [];
        foreach ($this->columns as $name => $column) {
            // If multiple formatters are defined, use the first for the header cell
            $field = !is_int($name) ? $this->model->getField($name) : null;

            if (!is_array($column)) {
                $column = [$column];
            }

            // we need to smartly wrap things up
            $cell = null;
            $cnt = count($column);
            $td_attr = [];
            foreach ($column as $c) {
                if (--$cnt) {
                    $html = $c->getDataCellTemplate($field);
                    $td_attr = $c->getTagAttributes('body', $td_attr);
                } else {
                    // Last formatter, ask it to give us whole rendering
                    $html = $c->getDataCellHtml($field, $td_attr);
                }

                if ($cell) {
                    if ($name) {
                        // if name is set, we can wrap things
                        $cell = str_replace('{$' . $name . '}', $cell, $html);
                    } else {
                        $cell = $cell . ' ' . $html;
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
