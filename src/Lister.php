<?php

namespace atk4\ui;

class Lister extends View
{
    use \atk4\core\HookTrait;

    // @var Template
    public $t_row = null;

    // @var Template
    public $t_totals = null;

    public $defaultTemplate = null;

    public function init()
    {
        parent::init();

        $this->initChunks();
    }

    public function initChunks()
    {
        $this->t_row = $this->template->cloneRegion('row');
        $this->template->del('rows');
    }

    public function renderView()
    {
        if (!$this->template) {
            throw new Exception(['Lister requires you to specify template explicitly']);
        }
        $this->t_row->trySet('_id', $this->name);
        //$this->t_totals = isset($this->template['totals']) ? $this->template->cloneRegion('totals') : null;


        foreach ($this->model as $this->current_id => $this->current_row) {
            if ($this->hook('beforeRow') === false) {
                continue;
            }


            $rowHTML = $this->t_row->set($this->current_row)->render();
            $this->template->appendHTML('rows', $rowHTML);
        }

        return parent::renderView(); //$this->template->render();
    }
}
