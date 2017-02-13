<?php

// vim:ts=4:sw=4:et:fdm=marker:fdl=0

namespace atk4\ui;

class Grid extends Lister
{
    use \atk4\core\HookTrait;


    public $defaultTemplate = 'grid.html';

    public $ui = 'table';

    public $current_row_html = [];

    public $default_column = '';

    public $columns = [];

    /**
     * Determines a strategy on how totals will be calculated.
     */
    public $totals_plan = false;

    public $totals = [];

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
    public function addColumn($name, $column_def = null, $field_def = null)
    {
        if (!$this->model) {
            $this->model = new \atk4\ui\misc\ProxyModel();
        }

        $field = $this->model->hasElement($name);

        if (!$field) {
            $field = $this->model->addField($name, $field_def);
        }

        if (!is_object($column_def)) {
            $column_def = $this->_columnFactory($field);
        } else {
            $this->add($column_def, $name);
        }

        $column_def->grid = $this;
        $this->columns[$name] = $column_def;

        return $column_def;
    }

    /**
     * Will come up with a column object based on the field object supplied. If
     * null is returned, then will use the default column.
     */
    public function _columnFactory(\atk4\data\Field $f)
    {
        switch ($f->type) {
        case 'boolean':
            return $this->add(new Column\Checkbox());

        default:
            return $this->default_column;
        }
    }

    /**
     * Overrides work like this:.
     *
     * [
     *   'name'=>'Totals for {$num} rows:',
     *   'price'=>'--',
     *   'total'=>['sum']
     * ]
     */
    public function addTotals($plan = [])
    {
        $this->totals_plan = $plan;
    }

    public $t_head;
    public $t_row;
    public $t_totals;
    public $t_empty;

    /**
     * Init method will create one column object that will be used to render
     * all columns in the grid unless you have specified a different
     * column object.
     */
    public function init()
    {
        parent::init();

        $this->t_head = $this->template->cloneRegion('Head');
        $this->t_row_master = $this->template->cloneRegion('Row');
        $this->t_totals = $this->template->cloneRegion('Totals');
        $this->t_empty = $this->template->cloneRegion('Empty');

        $this->template->del('Head');
        $this->template->del('Body');
        $this->template->del('Foot');

        $this->default_column = $this->add(new Column\Generic());
    }

    public function renderView()
    {
        $this->t_head->setHTML('cells', $this->renderHeaderCells());
        $this->template->setHTML('Head', $this->t_head->render());

        $this->t_row_master->setHTML('cells', $this->getRowTemplate());
        $this->t_row = new Template($this->t_row_master->render());

        $rows = 0;
        foreach ($this->model as $this->current_id => $tmp) {
            $this->current_row = $this->model->get();

            //$this->formatRow();

            if ($this->totals_plan) {
                $this->updateTotals();
            }

            $this->t_row->set($this->model);

            $html_tags = [];

            foreach($this->hook('getHTMLTags', [$this->model]) as $ret) {
                if(is_array($ret)) {
                    $html_tags = array_merge($html_tags, $ret);
                }
            }

            foreach ($this->columns as $name => $column) {
                if (!method_exists($column, 'getHTMLTags')) {
                    continue;
                }
                $field = $this->model->hasElement($name);
                $html_tags = array_merge($column->getHTMLTags($this->model, $field), $html_tags);
            }
            $this->t_row->setHTML($html_tags);

            $this->template->appendHTML('Body', $this->t_row->render());

            $this->t_row->del(array_keys($html_tags));

            $rows++;
        }

        if (!$rows) {
            $this->template->appendHTML('Body', $this->t_empty->render());
        } elseif ($this->totals_plan) {
            $this->t_totals->setHTML('cells', $this->renderTotalsCells());
            $this->template->appendHTML('Foot', $this->t_totals->render());
        } else {
        }

        return View::renderView();
    }

    public function updateTotals()
    {
        foreach ($this->totals_plan as $key=>$val) {
            if (is_array($val)) {
                switch ($val[0]) {
                case 'sum':
                    $this->totals[$key] += $this->model[$key];
                }
            }
        }
    }

    public function renderTotalsCells()
    {
        $output = [];
        foreach ($this->columns as $name => $column) {
            if (!isset($this->totals_plan[$name])) {
                $output[] = $this->app->getTag('th', '-');
                continue;
            }

            if (is_array($this->totals_plan[$name])) {
                // todo - format
                $output[] = $this->app->getTag('th', [], $this->columns[$name]->format($this->totals[$name]));
                continue;
            }

            $output[] = $this->app->getTag('th', [], $this->totals_plan[$name]);
        }

        return implode('', $output);
    }

    public function renderHeaderCells()
    {
        $output = [];
        foreach ($this->columns as $name => $column) {
            $field = $this->model->hasElement($name);

            $output[] = $column->getHeaderCell($field);
        }

        return implode('', $output);
    }

    /*
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
     */

    public function getRowTemplate()
    {
        $output = [];
        foreach ($this->columns as $name => $column) {
            $field = $this->model->hasElement($name);

            $output[] = $column->getCellTemplate($field);
        }

        return implode('', $output);
    }
}
