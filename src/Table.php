<?php

// vim:ts=4:sw=4:et:fdm=marker:fdl=0

namespace atk4\ui;

use atk4\ui\TableColumn\FilterPopup;

class Table extends Lister
{
    // Overrides
    public $defaultTemplate = 'table.html';
    public $ui = 'table';
    public $content = false;

    /**
     * If table is part of Grid or CRUD, we want to reload that instead of table.
     *
     * @var View|null Ususally a Grid or Crud view that contains the table.
     */
    public $reload = null;

    /**
     * Column objects can service multiple columns. You can use it for your advancage by re-using the object
     * when you pass it to addColumn(). If you omit the argument, then a column of a type 'Generic' will be
     * used.
     *
     * @var TableColumn\Generic
     */
    public $default_column = null;

    /**
     * Contains list of declared columns. Value will always be a column object.
     *
     * @var array
     */
    public $columns = [];

    /**
     * Allows you to inject HTML into table using getHTMLTags hook and column call-backs.
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
     * @var Template
     */
    public $t_head;

    /**
     * Contain the template for the "Body" type row.
     *
     * @var Template
     */
    public $t_row;

    /**
     * Contain the template for the "Foot" type row.
     *
     * @var Template
     */
    public $t_totals;

    /**
     * Contains the output to show if table contains no rows.
     *
     * @var Template
     */
    public $t_empty;

    /**
     * Set this if you want table to appear as sortable. This does not add any
     * mechanic of actual sorting - either implement manually or use Grid.
     *
     * @var null|bool
     */
    public $sortable = null;

    /**
     * When $sortable is true, you can specify which column will appear to have
     * active sorting on it.
     *
     * @var string
     */
    public $sort_by = null;

    /**
     * When $sortable is true, and $sort_by is set, you can set this to
     * "ascending" or "descending".
     *
     * @var string
     */
    public $sort_order = null;

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
     * @param null|string $class CSS class to add
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
     * @return TableColumn\Generic
     */
    public function addColumn($name, $columnDecorator = null, $field = null)
    {
        if (!$this->_initialized) {
            throw new Exception\NoRenderTree($this, 'addColumn()');
        }

        if (!$this->model) {
            $this->model = new \atk4\ui\misc\ProxyModel();
        }

        // This code should be vaugely consistent with FormLayout\Generic::addField()

        if (is_string($field)) {
            $field = ['type' => $field];
        }

        if (is_string($name) && $name) {
            $existingField = $this->model->hasElement($name);
        } else {
            $existingField = null;
        }

        if ($existingField === null) {
            // table column without respective field in model
            $field = null;
        } elseif (!$existingField) {
            // Add missing field
            if ($field) {
                $field = $this->model->addField($name, $field);
            } else {
                $field = $this->model->addField($name);
            }
            $field->never_persist = true;
        } elseif (is_array($field)) {
            // Add properties to existing field
            $existingField->setDefaults($field);
            $field = $existingField;
        } elseif (is_object($field)) {
            throw new Exception(['Duplicate field', 'name' => $name]);
        } else {
            $field = $existingField;
        }

        if ($field === null) {
            // column is not associated with any model field
            $columnDecorator = $this->_add($this->factory($columnDecorator, ['table' => $this], 'atk4\ui\TableColumn'));
        } elseif (is_array($columnDecorator) || is_string($columnDecorator)) {
            $columnDecorator = $this->decoratorFactory($field, $columnDecorator);
        } elseif (!$columnDecorator) {
            $columnDecorator = $this->decoratorFactory($field);
        } elseif (is_object($columnDecorator)) {
            if (!$columnDecorator instanceof \atk4\ui\TableColumn\Generic) {
                throw new Exception(['Column decorator must descend from \atk4\ui\TableColumn\Generic', 'columnDecorator' => $columnDecorator]);
            }
            $columnDecorator->table = $this;
            $this->_add($columnDecorator);
        } else {
            throw new Exception(['Value of $columnDecorator argument is incorrect', 'columnDecorator' => $columnDecorator]);
        }

        if (is_null($name)) {
            $this->columns[] = $columnDecorator;
        } elseif (!is_string($name)) {
            throw new Exception(['Name must be a string', 'name' => $name]);
        } elseif (isset($this->columns[$name])) {
            throw new Exception(['Table already has column with $name. Try using addDecorator()', 'name' => $name]);
        } else {
            $this->columns[$name] = $columnDecorator;
        }

        return $columnDecorator;
    }

    /**
     * Set Popup action for columns filtering.
     *
     * @param array $cols An array with colomns name that need filtering.
     *
     * @throws Exception
     * @throws \atk4\core\Exception
     */
    public function setFilterColumn($cols = null)
    {
        if (!$this->model) {
            throw new Exception('Model need to be defined in order to use column filtering.');
        }

        // set filter to all column when null.
        if (!$cols) {
            foreach ($this->model->getFields() as $key => $field) {
                if (isset($this->columns[$key]) && $this->columns[$key]) {
                    $cols[] = $field->short_name;
                }
            }
        }

        // create column popup.
        foreach ($cols as $colName) {
            $col = $this->columns[$colName];
            if ($col) {
                $pop = $col->addPopup(new FilterPopup(['field' => $this->model->getElement($colName), 'reload' => $this->reload, 'colTrigger' => '#'.$col->name.'_ac']));
                $pop->isFilterOn() ? $col->setHeaderPopupIcon('green caret square down') : null;
                $pop->form->onSubmit(function ($f) use ($pop) {
                    return new jsReload($this->reload);
                });
                //apply condition according to popup form.
                $this->model = $pop->setFilterCondition($this->model);
            }
        }
    }

    /**
     * Add column Decorator.
     *
     * @param string $name Column name
     * @param mixed  $seed Defaults to pass to factory() when decorator is initialized
     *
     * @return TableColumn\Generic
     */
    public function addDecorator($name, $seed)
    {
        if (!$this->columns[$name]) {
            throw new Exception(['No such column, cannot decorate', 'name' => $name]);
        }
        $decorator = $this->_add($this->factory($seed, ['table' => $this], 'atk4\ui\TableColumn'));

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
     *
     * @return array
     */
    public function getColumnDecorators($name)
    {
        $dec = $this->columns[$name];

        return is_array($dec) ? $dec : [$dec];
    }

    /**
     * Will come up with a column object based on the field object supplied.
     * By default will use default column.
     *
     * @param \atk4\data\Field $f    Data model field
     * @param mixed            $seed Defaults to pass to factory() when decorator is initialized
     *
     * @return TableColumn\Generic
     */
    public function decoratorFactory(\atk4\data\Field $f, $seed = [])
    {
        $seed = $this->mergeSeeds(
            $seed,
            isset($f->ui['table']) ? $f->ui['table'] : null,
            isset($this->typeToDecorator[$f->type]) ? $this->typeToDecorator[$f->type] : null,
            [$this->default_column ? $this->default_column : 'Generic']
        );

        return $this->_add($this->factory($seed, ['table' => $this], 'atk4\ui\TableColumn'));
    }

    protected $typeToDecorator = [
        'password' => 'Password',
        'money'    => 'Money',
        'text'     => 'Text',
        'boolean'  => ['Status', ['positive' => [true], 'negative' => [false]]],
    ];

    /**
     * Make columns resizable by dragging column header.
     *
     * The callback param function will receive two parameter, a jQuery chain object and a json string containing all table columns
     * name and size. To retrieve columns width, simply json decode the $widths param in your callback function.
     * ex:
     *  $table->resizableColumn(function($j, $w){
     *       //do somethings with columns width
     *       $columns = json_decode($w);
     *   });
     *
     *
     * @param null $fx             A callback function with columns widths as parameter.
     * @param null $widths         An array of widths value, integer only. ex: [100,200,300,100]
     * @param null $resizerOptions An array of column-resizer module options. see https://www.npmjs.com/package/column-resizer
     *
     * @throws Exception
     *
     * @return $this
     */
    public function resizableColumn($fx = null, $widths = null, $resizerOptions = null)
    {
        $options = [];
        if ($fx && is_callable($fx)) {
            $cb = $this->add('jsCallBack');
            $cb->set($fx, ['widths'=>'widths']);
            $options['uri'] = $cb->getJSURL();
        } elseif ($fx && is_array($fx)) {
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
     * @param int    $ipp          Number of item per page to start with.
     * @param array  $options      An array with js Scroll plugin options.
     * @param View   $container    The container holding the lister for scrolling purpose. Default to view owner.
     * @param string $scrollRegion A specific template region to render. Render output is append to container html element.
     *
     * @throws Exception
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
     * @param \atk4\data\Model $m       Data model
     * @param array|bool       $columns
     *
     * @return \atk4\data\Model
     */
    public function setModel(\atk4\data\Model $m, $columns = null)
    {
        parent::setModel($m);

        if ($columns === null) {
            $columns = array_keys($m->getFields('visible'));
        } elseif ($columns === false) {
            return $this->model;
        }

        foreach ($columns as $column) {
            $this->addColumn($column);
        }

        return $this->model;
    }

    /**
     * {@inheritdoc}
     */
    public function renderView()
    {
        if (!$this->columns) {
            throw new Exception(['Table does not have any columns defined', 'columns' => $this->columns]);
        }

        if ($this->sortable) {
            $this->addClass('sortable');
        }

        // Generate Header Row
        if ($this->header) {
            $this->t_head->setHTML('cells', $this->getHeaderRowHTML());
            $this->template->setHTML('Head', $this->t_head->render());
        }

        // Generate template for data row
        $this->t_row_master->setHTML('cells', $this->getDataRowHTML());
        $this->t_row_master['_id'] = '{$_id}';
        $this->t_row = new Template($this->t_row_master->render());
        $this->t_row->app = $this->app;

        // Iterate data rows
        $this->_rendered_rows_count = 0;
        foreach ($this->model as $this->current_id => $tmp) {
            $this->current_row = $this->model->get();
            if ($this->hook('beforeRow') === false) {
                continue;
            }

            if ($this->totals_plan) {
                $this->updateTotals();
            }

            $this->renderRow();

            $this->_rendered_rows_count++;

            if ($this->hook('afterRow') === false) {
                continue;
            }
        }

        // Add totals rows or empty message
        if (!$this->_rendered_rows_count) {
            if (!$this->jsPaginator || !$this->jsPaginator->getPage()) {
                $this->template->appendHTML('Body', $this->t_empty->render());
            }
        } elseif ($this->totals_plan) {
            $this->t_totals->setHTML('cells', $this->getTotalsRowHTML());
            $this->template->appendHTML('Foot', $this->t_totals->render());
        } else {
        }

        // stop jsPaginator if there are no more records to fetch
        if ($this->jsPaginator && ($this->_rendered_rows_count < $this->ipp)) {
            $this->jsPaginator->jsIdle();
        }

        return View::renderView();
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

            foreach ($this->hook('getHTMLTags', [$this->model]) as $ret) {
                if (is_array($ret)) {
                    $html_tags = array_merge($html_tags, $ret);
                }
            }

            foreach ($this->columns as $name => $columns) {
                if (!is_array($columns)) {
                    $columns = [$columns];
                }
                $field = $this->model->hasElement($name);
                foreach ($columns as $column) {
                    if (!method_exists($column, 'getHTMLTags')) {
                        continue;
                    }
                    $html_tags = array_merge($column->getHTMLTags($this->model, $field), $html_tags);
                }
            }

            // Render row and add to body
            $this->t_row->setHTML($html_tags);
            $this->t_row->set('_id', $this->model->id);
            $this->template->appendHTML('Body', $this->t_row->render());
            $this->t_row->del(array_keys($html_tags));
        } else {
            $this->template->appendHTML('Body', $this->t_row->render());
        }
    }

    /**
     * Same as on('click', 'tr', $action), but will also make sure you can't
     * click outside of the body. Additionally when you move cursor over the
     * rows, pointer will be used and rows will be highlighted as you hover.
     *
     * @param jsChain|callable|jsExpressionable $action Code to execute
     *
     * @return jQuery
     */
    public function onRowClick($action)
    {
        $this->addClass('selectable');
        $this->js(true)->find('tbody')->css('cursor', 'pointer');

        return $this->on('click', 'tbody>tr', $action);
    }

    /**
     * Use this to quickly access the <tr> and wrap in jQuery.
     *
     * $this->jsRow()->data('id');
     *
     * @return jQuery
     */
    public function jsRow()
    {
        return (new jQuery(new jsExpression('this')))->closest('tr');
    }

    /**
     * Remove a row in table using javascript using a model id.
     *
     * @param string $id         The model id where row need to be removed.
     * @param string $transition The transition effect.
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

            // if value is array, then we treat it as built-in or callable aggregate method
            if (is_array($val)) {
                $f = $val[0]; // shortcut

                // initial value is always 0
                if (!isset($this->totals[$key])) {
                    $this->totals[$key] = 0;
                }

                // closure support
                // arguments - current value, key, \atk4\ui\Table object
                if ($f instanceof \Closure) {
                    $this->totals[$key] += ($f($this->model[$key], $key, $this) ?: 0);
                }
                // built-in methods
                elseif (is_string($f)) {
                    switch ($f) {
                        case 'sum':
                            $this->totals[$key] += $this->model[$key];
                            break;
                        case 'count':
                            $this->totals[$key] += 1;
                            break;
                        case 'min':
                            if ($this->model[$key] < $this->totals[$key]) {
                                $this->totals[$key] = $this->model[$key];
                            }
                            break;
                        case 'max':
                            if ($this->model[$key] > $this->totals[$key]) {
                                $this->totals[$key] = $this->model[$key];
                            }
                            break;
                        default:
                            throw new Exception(['Aggregation method does not exist', 'method' => $f]);
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
    public function getHeaderRowHTML()
    {
        $output = [];
        foreach ($this->columns as $name => $column) {

            // If multiple formatters are defined, use the first for the header cell
            if (is_array($column)) {
                $column = $column[0];
            }

            if (!is_int($name)) {
                $field = $this->model->getElement($name);

                $output[] = $column->getHeaderCellHTML($field);
            } else {
                $output[] = $column->getHeaderCellHTML();
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
    public function getTotalsRowHTML()
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
                $field = $this->model->getElement($name);
                $output[] = $column->getTotalsCellHTML($field, $this->totals[$name]);
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
    public function getDataRowHTML()
    {
        $output = [];
        foreach ($this->columns as $name => $column) {

            // If multiple formatters are defined, use the first for the header cell

            if (!is_int($name)) {
                $field = $this->model->getElement($name);
            } else {
                $field = null;
            }

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
                    $html = $c->getDataCellHTML($field, $td_attr);
                }

                if ($cell) {
                    if ($name) {
                        // if name is set, we can wrap things
                        $cell = str_replace('{$'.$name.'}', $cell, $html);
                    } else {
                        $cell = $cell.' '.$html;
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
