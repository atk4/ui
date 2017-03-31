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
     * @var Menu
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
     * @var TableColumn\Action
     */
    public $actions = null;

    /**
     * Calling addSelection will add a new column inside $table, containing checkboxes.
     * This column will be stored here, in case you want to access it.
     *
     * @var TableColumn\Checkbox
     */
    public $selection = null;

    /**
     * Component that actually renders data rows / coluns and possibly totals.
     *
     * @var Table
     */
    public $table = null;

    public $defaultTemplate = 'grid.html';

    public function init()
    {
        parent::init();

        if (is_null($this->menu)) {
            $this->menu = $this->add(['Menu', 'activate_on_click'=>false], 'Menu');
        }

        if (is_null($this->table)) {
            $this->table = $this->add(['Table', 'very compact'], 'Table');
        }

        if (is_null($this->paginator)) {
            $seg = $this->add(['View'], 'Paginator')->addStyle('text-align', 'center');
            $this->paginator = $seg->add('Paginator');
        }
    }

    public function addButton($text)
    {
        return $this->menu->addItem()->add(new Button($text));
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

        $this->quickSearch = $form->add(new \atk4\ui\FormField\Input(['placeholder'=>'Search', 'short_name'=>$this->name.'_q', 'icon'=>'search']))
            ->addClass('transparent');

        if (isset($_GET[$this->name.'_q'])) {
            $q = $_GET[$this->name.'_q'];
            $this->quickSearch->set($q);

            $cond = [];
            foreach ($fields as $field) {
                $cond[] = [$field, 'like', '%'.$q.'%'];
            }
            $this->model->addCondition($cond);
        }
    }

    public function addAction($label, $action)
    {
        if (!$this->actions) {
            $this->actions = $this->table->addColumn('TableColumn/Actions');
        }

        $this->actions->addAction($label, $action);
    }

    public function setModel(\atk4\data\Model $model, $columns = null)
    {
        return $this->model = $this->table->setModel($model, $columns);
    }

    public function addSelection()
    {
        $this->selection = $this->table->addColumn('TableColumn/Checkbox');

        // Move element to the beginning
        $k = array_search($this->selection, $this->table->columns);
        $this->table->columns = [$k => $this->table->columns[$k]] + $this->table->columns;

        return $this->selection;
    }

    public function recursiveRender()
    {
        // bind with paginator

        if ($this->paginator) {
            $this->paginator->reload = $this;

            $this->paginator->setTotal(ceil($this->model->action('count')->getOne() / $this->ipp));

            $this->model->setLimit($this->ipp, ($this->paginator->page - 1) * $this->ipp);
        }

        return parent::recursiveRender();
    }
}
