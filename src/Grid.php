<?php

// vim:ts=4:sw=4:et:fdm=marker:fdl=0

namespace atk4\ui;

/**
 * Implements a more sophisticated and interractive Data-Table component.
 */
class Grid extends View
{
    /**
     * Will be initalized to Menu object, however you can set this to false to disable menu.
     *
     * @var Menu|false
     */
    public $menu = null;

    /**
     * Calling addQuickSearch will create a form with a field inside $menu to perform quick searches.
     *
     * @var FormField\Generic
     */
    public $quickSearch = null;

    /**
     * Paginator is automatically added below the table and will provide
     * divide long tables into pages.
     *
     * @var Paginator|false
     */
    public $paginator = null;

    /**
     * Number of items per page to display.
     *
     * @var int
     */
    public $ipp = 50;

    /**
     * Calling addAction will add a new column inside $table, and will be re-used
     * for next addAction().
     *
     * @var TableColumn\Actions
     */
    public $actions = null;

    /**
     * Calling addSelection will add a new column inside $table, containing checkboxes.
     * This column will be stored here, in case you want to access it.
     *
     * @var TableColumn\CheckBox
     */
    public $selection = null;

    /**
     * Grid can be sorted by clicking on column headers. This will be automatically enabled
     * if Model supports ordering. You may override by setting true/false.
     *
     * @var bool
     */
    public $sortable = null;

    /**
     * Component that actually renders data rows / coluns and possibly totals.
     *
     * @var Table|false
     */
    public $table = null;

    /**
     * The container for table and paginator.
     *
     * @var View
     */
    public $container = null;

    public $defaultTemplate = 'grid.html';

    public function init()
    {
        parent::init();

        $this->container = $this->add(['View', 'ui'=>'', 'template' => new Template('<div id="{$_id}"><div class="ui table atk-overflow-auto">{$Table}</div>{$Paginator}</div>')]);

        if (is_null($this->menu)) {
            $this->menu = $this->add(['Menu', 'activate_on_click' => false], 'Menu');
        }

        if (is_null($this->table)) {
            $this->table = $this->container->add(['Table', 'very compact striped single line', 'reload' => $this], 'Table');
        }

        if (is_null($this->paginator)) {
            $seg = $this->container->add(['View'], 'Paginator')->addStyle('text-align', 'center');
            $this->paginator = $seg->add(['Paginator', 'reload' => $this]);
        }
    }

    /**
     * Add new column to grid. If column with this name already exists,
     * an. Simply calls Table::addColumn(), so check that method out.
     *
     * @param string                   $name            Data model field name
     * @param array|string|object|null $columnDecorator
     * @param array|string|object|null $field
     *
     * @return Column\Generic
     */
    public function addColumn($name, $columnDecorator = null, $field = null)
    {
        return $this->table->addColumn($name, $columnDecorator, $field);
    }

    public function addDecorator($name, $decorator)
    {
        return $this->table->addDecorator($name, $decorator);
    }

    public function addButton($text)
    {
        return $this->menu->addItem()->add(new Button($text));
    }

    /**
     * Add Search input field using js action.
     *
     * @param array $fields
     *
     * @throws Exception
     * @throws \atk4\data\Exception
     */
    public function addJsSearch($fields = [])
    {
        if (!$fields) {
            $fields = [$this->model->title_field];
        }

        if (!$this->menu) {
            throw new Exception(['Unable to add QuickSearch without Menu']);
        }

        $view = $this->menu
            ->addMenuRight()->addItem()->setElement('div')
            ->add('View');

        $this->quickSearch = $view->add(['jsSearch', 'reload' => $this->container]);

        if ($q = $this->stickyGet('_q')) {
            $cond = [];
            foreach ($fields as $field) {
                $cond[] = [$field, 'like', '%'.$q.'%'];
            }
            $this->model->addCondition($cond);
        }
    }

    public function addQuickSearch($fields = [])
    {
        if (!$fields) {
            $fields = [$this->model->title_field];
        }
        if (!$this->menu) {
            throw new Exception(['Unable to add QuickSearch without Menu']);
        }
        $form = $this->menu
            ->addMenuRight()->addItem()->setElement('div')
            ->add('View')->setElement('form');
        $this->quickSearch = $form->add(new \atk4\ui\FormField\Input(['placeholder' => 'Search', 'short_name' => $this->name.'_q', 'icon' => 'search']))
                                  ->addClass('transparent');
        if ($q = $this->stickyGet($this->name.'_q')) {
            $this->quickSearch->set($q);
            $cond = [];
            foreach ($fields as $field) {
                $cond[] = [$field, 'like', '%'.$q.'%'];
            }
            $this->model->addCondition($cond);
        }
    }

    public function addAction($button, $action, $confirm = false)
    {
        if (!$this->actions) {
            $this->actions = $this->table->addColumn(null, 'Actions');
        }

        return $this->actions->addAction($button, $action, $confirm);
    }

    public function addModalAction($button, $title, $callback)
    {
        if (!$this->actions) {
            $this->actions = $this->table->addColumn(null, 'Actions');
        }

        return $this->actions->addModal($button, $title, $callback, $this);
    }

    /**
     * Apply ordering to the current model as per the sort parameters.
     */
    public function applySort()
    {
        //$sortby = $this->app->stickyGET($this->name.'_sort', null);
        $sortby = $this->stickyGet($this->name.'_sort');
        $desc = false;
        if ($sortby && $sortby[0] == '-') {
            $desc = true;
            $sortby = substr($sortby, 1);
        }

        $this->table->sortable = true;

        if ($sortby && isset($this->table->columns[$sortby]) && $this->model->hasElement($sortby) instanceof \atk4\data\Field) {
            $this->model->setOrder($sortby, $desc);
            $this->table->sort_by = $sortby;
            $this->table->sort_order = $desc ? 'descending' : 'ascending';
        }

        $this->table->on('click', 'thead>tr>th', new jsReload($this->container, [$this->name.'_sort' => (new jQuery())->data('column')]));
    }

    public function setModel(\atk4\data\Model $model, $columns = null)
    {
        $this->model = $this->table->setModel($model, $columns);
        if ($this->sortable === null) {
            $this->sortable = true;
        }
        if ($this->sortable) {
            $this->applySort();
        }
        if ($this->quickSearch && is_array($this->quickSearch)) {
            $this->addQuickSearch($this->quickSearch);
        }

        return $this->model;
    }

    public function addSelection()
    {
        $this->selection = $this->table->addColumn(null, 'CheckBox');

        // Move element to the beginning
        $k = array_search($this->selection, $this->table->columns);
        $this->table->columns = [$k => $this->table->columns[$k]] + $this->table->columns;

        return $this->selection;
    }

    public function recursiveRender()
    {
        // bind with paginator
        if ($this->paginator) {
            $this->paginator->reload = $this->container;

            $this->paginator->setTotal(ceil($this->model->action('count')->getOne() / $this->ipp));

            $this->model->setLimit($this->ipp, ($this->paginator->page - 1) * $this->ipp);
        }

        if ($this->quickSearch instanceof jsSearch) {
            if ($sortby = $this->stickyGet($this->name.'_sort')) {
                $this->container->js(true, $this->quickSearch->js()->atkJsSearch('setSortArgs', [$this->name.'_sort', $sortby]));
            }
        }

        return parent::recursiveRender();
    }

    /**
     * Proxy function for Table::jsRow().
     */
    public function jsRow()
    {
        return $this->table->jsRow();
    }
}
