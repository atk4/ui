<?php

namespace atk4\ui;

class Lister extends View
{
    use \atk4\core\HookTrait;

    // @var Template
    public $t_row = null;

    public $defaultTemplate = null;

    public function init()
    {
        parent::init();

        $this->initChunks();
    }

    public function initChunks()
    {
        if (!$this->template) {
            throw new Exception(['Lister does not have default template. Either supply your own HTML or use "defaultTemplate"=>"lister.html"']);
        }
        if ($this->template->hasTag('row')) {
            $this->t_row = $this->template->cloneRegion('row');
            $this->template->del('rows');
        } else {
            $this->t_row = $this->template;
        }
    }

    public function renderView()
    {
        if (!$this->template) {
            throw new Exception(['Lister requires you to specify template explicitly']);
        }
        $this->t_row->trySet('_id', $this->name);
        $rowHTML = '';

        foreach ($this->model as $this->current_id => $this->current_row) {
            if ($this->hook('beforeRow') === false) {
                continue;
            }

            if ($this->t_row->hasTag('_title')) {
                $this->t_row->set('_title', $this->model[$this->model->title_field]);
            }

            if ($this->t_row->hasTag('_href')) {
                $this->t_row->set('_href', $this->app->url(['id'=>$this->current_id]));
            }

            if ($this->t_row->hasTag('_id')) {
                $this->t_row->set('_id', $this->current_id);
            }

            if ($this->t_row == $this->template) {
                $rowHTML .= $this->t_row->set($this->current_row)->render();
            } else {
                $rowHTML = $this->t_row->set($this->current_row)->render();
                $this->template->appendHTML('rows', $rowHTML);
            }
        }

        if ($this->t_row == $this->template) {
            $this->template = new Template('{$c}');
            $this->template->setHTML('c', $rowHTML);

            // for some reason this does not work:
            //$this->template->set('_top', $rowHTML);
        }

        return parent::renderView(); //$this->template->render();
    }
}
