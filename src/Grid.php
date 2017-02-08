<?php

namespace atk4\ui;

class Grid extends Lister
{
    public $defaultTemplate = 'grid.html';

    public $current_row_html = [];

    public $columns = [];

    public $ui = 'table';

    public $compact = 'very compact';

    /**
     * Defines a new column for this field. You need two objects for field to
     * work.
     *
     * First is being Model field. If your Grid is already associated
     * with the model, it will automatically pick one by looking up element
     * corresponding to the $name.
     *
     * The other object is a Column. This object know how to produce HTML
     * for cells and will handle other things like alignment. If you do not specify
     * column, then it will be selected dynamically based on field type.
     */
    public function addColumn($name, $field_def = [], $column_def = null)
    {
        if (!$this->model) {
            $this->model = new \atk4\ui\misc\ProxyModel();
        }

        $field = $this->model->hasElement($name);

        if (!$field) {
            $field = $this->model->addField($name, $field_def);
        }

        if (!is_object($column_def)) {
            $column_def = $this->add('Column/'.($column_def ?: 'Generic'), $name);
        } else {
            $this->add($column_def, $name);
        }

        $this->columns[$name] = $column_def;

        return $column_def;
    }

    /**
     * Will come up with a column object based on the field object supplied.
     */
    public function _columnFactory(\atk4\data\Field $f)
    {
        switch ($f->type) {
        case 'boolean':
            return new Column\Checkbox(['grid'=>$this, 'field'=>$f, 'short_name'=>$f->short_name]);

        default:
            return new Column\Generic(['grid'=>$this, 'field'=>$f, 'short_name'=>$f->short_name]);
        }
    }

    public $t_head;
    public $t_row;
    public $t_totals;
    public $t_empty;

    public function init()
    {
        parent::init();

        $this->t_head = $this->template->cloneRegion('Head');
        $this->t_row = $this->template->cloneRegion('Row');
        $this->t_totals = $this->template->cloneRegion('Totals');
        $this->t_empty = $this->template->cloneRegion('Empty');

        $this->template->del('Head');
        $this->template->del('Body');
        $this->template->del('Foot');
    }

    public function renderView()
    {
        $this->t_head->setHTML('cells', $this->renderHeaderCells());
        $this->template->setHTML('Head', $this->t_head->render());

        $rows = 0;
        foreach ($this->model as $this->current_id => $tmp) {
            $this->current_row = $this->model->get();

            $this->formatRow();

            $this->t_row->setHTML('cells', $this->renderCells());

            $this->template->appendHTML('Body', $this->t_row->render());
            $rows++;
        }

        if (!$rows) {
            $this->template->appendHTML('Body', $this->t_empty->render());
        }

        return View::renderView();
    }

    public function renderHeaderCells()
    {
        $output = [];
        foreach ($this->columns as $name => $column) {
            $output[] = $this->app->getTag('th', [], $name);
        }

        return implode('', $output);
    }

    public function renderCells()
    {
        $output = [];
        foreach ($this->columns as $name => $column) {
            $html = isset($this->current_row_html[$name]) ?
                $this->current_row_html : $this->app->encodeHTML($this->current_row[$name]);

            $output[] = $this->app->getTag('td', [], $html);
        }

        return implode('', $output);
    }

    public function formatRow()
    {
        $this->current_row_html = [];

        foreach ($this->columns as $name => $column) {
            $value = isset($this->current_row[$name]) ? $this->current_row[$name] : null;

            $this->current_row[$name] = $column->format($value, $name, $this->current_row, $this->current_row_html);
        }
    }
}
