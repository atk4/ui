<?php

namespace atk4\ui\Component;

use atk4\ui\jsVueService;
use atk4\ui\View;

/**
 * Will send query with define callback and reload a specific view.
 */
class ItemSearch extends View
{
    /**
     * View to be reload that contains data to be filtered.
     *
     * @var View|string The atk4 View to be reload or a jquery id selector string.
     */
    public $reload = null;

    /**
     * The initial query.
     *
     * @var null
     */
    public $q = null;

    /**
     * The css for the input field.
     *
     * @var string
     */
    public $inputCss = 'ui input right icon transparent';

    /**
     * The jquery selector where you need to add the semantic-ui 'loading' class.
     * Default to reload selector.
     *
     * @var null
     */
    public $context = null;

    public $defaultTemplate = 'item-search.html';

    public function init()
    {
        parent::init();

        if (!$this->q) {
            $this->q = $this->getQuery();
        }
    }

    /**
     * Return query string sent by request.
     */
    public function getQuery()
    {
        return $_GET['_q'] ? $_GET['_q'] : null;
    }

    /**
     * Set model condition base on search request.
     *
     * @param $m
     *
     * @return mixed
     */
    public function setModelCondition($m)
    {
        $q = $this->getQuery();
        if ($q && ($_GET['__atk_reload'] ? $_GET['__atk_reload'] : null) === $this->reload->name) {
            $m->addCondition('name', 'like', '%'.$q.'%');
        }

        return $m;
    }

    public function renderView()
    {
        $this->class = [];
        $this->template->set('inputCss', $this->inputCss);
        parent::renderView();

        // reloadId is the view id selector name that need to be reload.
        // this will be pass as get argument to __atk_reload.
        if ($this->reload instanceof View) {
            $reloadId = $this->reload->name;
        } else {
            $reloadId = $this->reload;
        }

        $this->js(true, (new jsVueService())->createAtkVue('#'.$this->name,
                                                      'atk-item-search',
                                                      [
                                                          'reload'   => $reloadId,
                                                          'url'      => $this->reload->jsURL(),
                                                          'q'        => $this->q,
                                                          'context'  => $this->context,
                                                      ]
        )
        );
    }
}
