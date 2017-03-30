<?php

// vim:ts=4:sw=4:et:fdm=marker:fdl=0

namespace atk4\ui;

/**
 * Implements a more sophisticated and interractive Data-Table component
 */
class Grid extends View
{
    public $menu = null;

    public $quickSearch = null;

    public $table = null;

    public $buttons = null;

    public $actions = null;

    public $defaultTemplate = 'grid.html';

    public $ipp = 20;

    public $paginator = null;

    public $selection = null;

    function init() {
        parent::init();

        if (!$this->menu) {
            $this->menu = $this->add(['Menu', 'activate_on_click'=>false], 'Menu');
        }

        if (!$this->table) {
            $this->table = $this->add(['Table', 'very compact'], 'Table');
        }


    }

    function addButton($text) {
        return $this->menu->addItem()->add(new Button($text));
    }

    function addQuickSearch($fields = []) {
        if (!$fields) {
            $fields = [$this->model->title_field];
        }

        $x = $this->menu->addMenuRight();
        $this->quickSearch = $x->addItem()->add(new \atk4\ui\FormField\Input(['placeholder'=>'Search', 'icon'=>'search']))->addClass('transparent');
    }

    function addAction($label, $action) {
        if (!$this->actions) {
            $this->actions = $this->table->addColumn('TableColumn/Actions');
        }

        $this->actions->addAction($label, $action);
    }

    function setModel(\atk4\data\Model $model, $columns = null) {
        return $this->model = $this->table->setModel($model, $columns)->setLimit($this->ipp);
    }

    function addSelection() {
        $this->selection =  $this->table->addColumn('TableColumn/Checkbox');

        // Move element to the beginning
        $k = array_search($this->selection, $this->table->columns);
        $this->table->columns = [$k => $this->table->columns[$k]] + $this->table->columns;

        return $this->selection;
    }
}
