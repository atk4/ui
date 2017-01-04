<?php

namespace atk4\ui;

class Lister extends View
{
    public $t_row = null;
    public $t_totals = null;

    public $template = null;

    public function renderView()
    {
        $this->t_row = $this->template->cloneRegion('row');
        //$this->t_totals = isset($this->template['totals']) ? $this->template->cloneRegion('totals') : null;

        $this->template->del('rows');

        foreach ($this->model as $this->current_id => $this->current_row) {
            $row_html = $this->t_row->set($this->current_row)->render();
            $this->template->appendHTML('rows', $row_html);
        }

        return parent::renderView(); //$this->template->render();
    }
}
