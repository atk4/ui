<?php

// vim:ts=4:sw=4:et:fdm=marker:fdl=0

namespace atk4\ui;

class Table extends Lister
{
    // Overrides
    public $defaultTemplate = 'table.html';
    public $ui = 'table';
    public $content = false;

    /**
     * TODO: is this used??
     *
     * @var null
     *
     * If table is part of Grid or CRUD, we want to reload that instead of grid.
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
     * Setting this to false will hide header row.
     *
     * @var bool
     */
    public $header = true;

    /**
     * Determines a strategy on how totals will be calculated.
     *
     * Do not touch those fields directly, instead use addTotals() or setTotals().
     *
     * @var array
     */
    public $totals_plan = [];

    /**
     * Contains list of totals accumulated during the render process.
     *
     * Don't use this property directly. Use addTotals() and setTotals() instead.
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

    public function __construct($class = null)
    {
        if ($class) {
            $this->addClass($class);
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
     * @param string                   $name            Data model field name
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

        // This code should be vaguely consistent with FormLayout\Generic::addField()

        if (is_string($field)) {
            $field = ['type' => $field];
        }

        if ($name) {
            $existingField = $this->model->hasElement($name);
        } else {
            $existingField = null;
        }

        if (!$existingField) {
            // Add missing field
            if ($field) {
                $field = $this->model->addField($name, $field);
                $field->never_persist = true;
            } else {
                $field = $this->model->addField($name);
                $field->never_persist = true;
            }
        } elseif (is_array($field)) {
            // Add properties to existing field
            $existingField->setDefaults($field);
            $field = $existingField;
        } elseif (is_object($field)) {
            throw new Exception(['Duplicate field', 'name' => $name]);
        } else {
            $field = $existingField;
        }

        if (is_array($columnDecorator) || is_string($columnDecorator)) {
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
            echo 'about to throw exception.....';

            throw new Exception(['Name must be a string', 'name' => $name]);
        } elseif (isset($this->columns[$name])) {
            throw new Exception(['Table already has column with $name. Try using addDecorator()', 'name' => $name]);
        } else {
            $this->columns[$name] = $columnDecorator;
        }

        return $columnDecorator;
    }

    /**
     * Add column Decorator.
     *
     * @param string                     $name      Column name
     * @param string|TableColumn/Generic $decorator
     */
    public function addDecorator($name, $decorator)
    {
        if (!$this->columns[$name]) {
            throw new Exception(['No such column, cannot decorate', 'name' => $name]);
        }
        $decorator = $this->_add($this->factory($decorator, ['table' => $this], 'TableColumn'));

        if (!is_array($this->columns[$name])) {
            $this->columns[$name] = [$this->columns[$name]];
        }
        $this->columns[$name][] = $decorator;
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
        if (!is_array($dec)) {
            $dec = [$dec];
        }

        return $dec;
    }

    /**
     * Will come up with a column object based on the field object supplied.
     * By default will use default column.
     *
     * @param \atk4\data\Field $f    Data model field
     * @param array            $seed Defaults to pass to factory() when decorator is initialized
     *
     * @return TableColumn\Generic
     */
    public function decoratorFactory(\atk4\data\Field $f, $seed = [])
    {
        $seed = $this->mergeSeeds(
            $seed,
            isset($f->ui['table']) ? $f->ui['table'] : null,
            isset($this->typeToDecorator[$f->type]) ? $this->typeToDecorator[$f->type] : null,
            ['Generic']
        );

        return $this->_add($this->factory($seed, ['table' => $this], 'TableColumn'));
    }

    protected $typeToDecorator = [
        'password' => 'Password',
        'money'    => 'Money',
        'text'     => 'Text',
        'boolean'  => ['Status', ['positive' => [true], 'negative' => ['false']]],
    ];

    /**
     * Adds totals calculation plan.
     * You can call this method multiple times to add more than one totals row.
     *
     * It returns totals plan ID which you can use to do some magic in rendering phase.
     *
     * @param array      $plan    Array of type [column => strategy]
     * @param string|int $plan_id Optional totals plan id
     *
     * @return string|int
     */
    public function addTotals($plan = [], $plan_id = null)
    {
        // normalize plan
        foreach ($plan as $field => $def) {
            // title (string or callable)
            if (is_string($def) || is_callable($def)) {
                $plan[$field] = ['title' => $def];
            }

            // "row" strategy + defaults
            if (is_array($def) && isset($def[0])) {
                $plan[$field]['row'] = $def[0];
                unset($plan[$field][0]);
            }
        }

        // each plan can have some "hidden" columns
        // we use them, for example, to count table rows while rendering
        $plan['_row_count']['row'] = 'count';

        // save normalized plan
        if ($plan_id !== null) {
            $this->totals_plan[$plan_id] = $plan;
        } else {
            $this->totals_plan[] = $plan;
            $plan_id = max(array_filter(array_keys($this->totals_plan), 'is_int'));
        }

        //var_dump($this->totals_plan);

        return $plan_id;
    }

    /**
     * Sets totals calculation plans [$plan_id=>[column=>strategy]].
     * This will overwrite all previously set plans.
     *
     * @param array $plans
     *
     * @return $this
     */
    public function setTotals($plans = [])
    {
        // reset
        $this->totals_plan = [];

        // add each plan
        foreach ($plans as $plan_id => $plan) {
            $this->addTotals($plan, $plan_id);
        }

        return $this;
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
            $columns = [];
            foreach ($m->elements as $name => $element) {
                if (!$element instanceof \atk4\data\Field) {
                    continue;
                }

                if ($element->isVisible()) {
                    $columns[] = $name;
                }
            }
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
        $rows = 0;
        foreach ($this->model as $this->current_id => $tmp) {
            $this->current_row = $this->model->get();
            if ($this->hook('beforeRow') === false) {
                continue;
            }

            if ($this->totals_plan) {
                $this->updateTotals();
            }

            $this->renderRow();

            $rows++;
        }

        // Add totals rows or empty message
        if (!$rows) {
            $this->template->appendHTML('Body', $this->t_empty->render());
        } elseif ($this->totals_plan) {
            foreach (array_keys($this->totals_plan) as $plan_id) {
                $this->t_totals->setHTML('cells', $this->getTotalsRowHTML($plan_id));
                $this->template->appendHTML('Foot', $this->t_totals->render());
            }
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
                    if (method_exists($column, 'getHTMLTags')) {
                        $html_tags = array_merge($column->getHTMLTags($this->model, $field), $html_tags);
                    }
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
     * @param jsChain|callable $action Code to execute
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
     * Executed for each row if "totals" are enabled to add up values.
     * It will calculate requested totals for all total plans.
     */
    public function updateTotals()
    {
        foreach ($this->totals_plan as $plan_id => $plan) {
            $t = &$this->totals[$plan_id]; // shortcut
            if (!$t) {
                $t = [];
            }

            // update totals for each column
            foreach ($plan as $key => $def) {
                // ignore column if "row" strategy is not set
                if (!isset($def['row'])) {
                    continue;
                }

                // simply initialize array key, but don't set any value
                // we can't set initial value to 0, because min/max or some custom totals
                // methods can use this 0 as value for comparison and that's wrong
                if (!array_key_exists($key, $t)) {
                    if (isset($def['default'])) {
                        $d = $def['default']; // shortcut

                        $t[$key] = is_callable($d)
                            ? call_user_func_array($d, [$this->model[$key], $this->model])
                            : $d;
                    } else {
                        $t[$key] = null;
                    }
                }

                // calc row totals
                $f = $def['row']; // shortcut

                // built-in functions
                if (is_string($f)) {
                    switch ($f) {
                        case 'sum':
                            // set initial value
                            if ($t[$key] === null) {
                                $t[$key] = 0;
                            }
                            // sum
                            $t[$key] = $t[$key] + $this->model[$key];
                            break;
                        case 'count':
                            // set initial value
                            if ($t[$key] === null) {
                                $t[$key] = 0;
                            }
                            // increment
                            $t[$key]++;
                            break;
                        case 'min':
                            // set initial value
                            if ($t[$key] === null) {
                                $t[$key] = $this->model[$key];
                            }
                            // compare
                            if ($this->model[$key] < $t[$key]) {
                                $t[$key] = $this->model[$key];
                            }
                            break;
                        case 'max':
                            // set initial value
                            if ($t[$key] === null) {
                                $t[$key] = $this->model[$key];
                            }
                            // compare
                            if ($this->model[$key] > $t[$key]) {
                                $t[$key] = $this->model[$key];
                            }
                            break;
                        default:
                            throw new Exception([
                                'Aggregation method does not exist',
                                'column' => $key,
                                'method' => $f,
                            ]);
                    }

                    continue;
                }

                // Callable support
                // Arguments:
                // - current total value
                // - current field value from model
                // - \atk4\data\Model table model with current record loaded
                // Should return new total value (for example, current value + current field value)
                // NOTE: Keep in mind, that current total value initially can be null !
                if (is_callable($f)) {
                    $t[$key] = call_user_func_array($f, [$t[$key], $this->model[$key], $this->model]);

                    continue;
                }
            }//foreach $plan
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
     * contain totals for all columns. This generates only one totals
     * row for particular totals plan with $plan_id.
     *
     * @param string|int $plan_id
     *
     * @return string
     */
    public function getTotalsRowHTML($plan_id)
    {
        // shortcuts
        $plan = &$this->totals_plan[$plan_id];
        $totals = &$this->totals[$plan_id];

        $output = [];
        foreach ($this->columns as $name => $column) {
            $field = $this->model->getElement($name);

            // if no totals plan, then add empty cell, but keep column formatting
            if (!isset($plan[$name])) {
                $output[] = $column->getTotalsCellHTML($field, '', false);
                continue;
            }

            // if totals was calculated, then show formatted value
            if (array_key_exists($name, $totals)) {
                $output[] = $column->getTotalsCellHTML($field, $totals[$name], true);
                continue;
            }

            // if no title set in totals plan, then add empty cell, but keep column formatting
            if (!isset($plan[$name]['title'])) {
                $output[] = $column->getTotalsCellHTML($field, '', false);
                continue;
            }

            // if title is set then just show it, for example, "Totals:" cell
            // this can be passed as string or callable
            $title = '';
            if (is_string($plan[$name]['title'])) {
                $title = $plan[$name]['title'];
            } elseif (is_callable($plan[$name]['title'])) {
                $title = call_user_func_array($plan[$name]['title'], [$totals, $this->model]);
            }

            // title can be defined as template and we fill in other total values if needed
            $title = new Template($title);
            $title->set($totals);

            $output[] = $column->getTotalsCellHTML($field, $title->render(), false);
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
