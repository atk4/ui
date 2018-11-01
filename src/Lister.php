<?php

namespace atk4\ui;

class Lister extends View
{
    use \atk4\core\HookTrait;

    /**
     * Lister repeats part of it's template. This property will contain
     * the repeating part. Clones from {row}. If your tempalte does not
     * have {row} tag, then entire temlate will be repeated.
     *
     * @var Template
     */
    public $t_row = null;

    public $defaultTemplate = null;

    public function init()
    {
        parent::init();

        $this->initChunks();
    }

    /**
     * Add Dynamic paginator when scrolling content via Javascript.
     * Will output x item in lister set per ipp until user scroll content to the end of page.
     * When this happen, content will be reload x number of items.
     *
     * @param $ipp                  //number of item per page
     * @param null $container //the container holding the lister for scrolling purpose. Default to view owner.
     * @param $options     // An array with js Scroll plugin options.
     * @param null $scrollRegion //A specific template region to render. Render output is append to container html element.
     *
     * @throws Exception
     */
    public function addJsPaginator($ipp, $container = null, $options = null, $scrollRegion = null)
    {
        $scrollable = $this->add(['jsPaginator', 'view' => $container, 'options' => $options]);
        $scrollable->onScroll(function ($p) use ($ipp, $scrollRegion) {
            if ($p - 1 < ceil($this->model->action('count')->getOne() / $ipp)) {
                $this->model->setLimit($ipp, ($p - 1) * $ipp);
                $this->app->terminate($this->renderJSON(true, $scrollRegion));
            } else {
                $this->app->terminate(json_encode(['success' => true, 'message' => 'done', 'html' => null]));
            }
        });
        $this->model->setLimit($ipp);
    }

    /**
     * From the current template will extract {row} into $this->t_row.
     *
     * @return void
     */
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

        // if no model is set, don't show anything (even warning)
        if (!$this->model) {
            return parent::renderView();
        }

        foreach ($this->model as $this->current_id => $this->current_row) {
            if ($this->hook('beforeRow') === false) {
                continue;
            }

            $this->t_row->trySet('_title', $this->model->getTitle());
            $this->t_row->trySet('_href', $this->url(['id'=>$this->current_id]));
            $this->t_row->trySet('_id', $this->current_id);

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
