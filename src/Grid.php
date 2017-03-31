<?php

// vim:ts=4:sw=4:et:fdm=marker:fdl=0

namespace atk4\ui;

/**
 * Implements a more sophisticated and interractive Data-Table component.
 */
class Grid extends View
{
    public $menu = null;

    public $quickSearch = null;

    public $table = null;

    public $buttons = null;

    public $actions = null;

    public $defaultTemplate = 'grid.html';

    public $ipp = 50;

    public $paginator = null;

    public $selection = null;

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
            $seg = $this->add(['View', 'ui'=>'segment'], 'Paginator')->addClass('center aligned basic');
            $this->paginator = $seg->add(['Paginator', 'ipp'=>$this->ipp]);
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

        $x = $this->menu->addMenuRight();
        $this->quickSearch = $x->addItem()->setElement('div')
            ->add(new \atk4\ui\FormField\Input(['placeholder'=>'Search', 'icon'=>'search']))->addClass('transparent');
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
            if ($this->ipp) {
                $this->paginator->ipp = $this->ipp;
            }

            $this->paginator->setTotal(ceil($this->model->action('count')->getOne() / $this->paginator->ipp));

            $this->model->setLimit($this->paginator->ipp, ($this->paginator->page - 1) * $this->paginator->ipp);
        }

        return parent::recursiveRender();
    }
}
