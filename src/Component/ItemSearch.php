<?php

namespace atk4\ui\Component;

use atk4\data\Model;
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
    public $reload;

    /**
     * The initial query.
     *
     * @var string
     */
    public $q;

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
    public $context;

    /** @var string|null The URL argument name use for query. If null, then->>name will be assiged. */
    public $queryArg;

    public $defaultTemplate = 'item-search.html';

    public function init(): void
    {
        parent::init();

        if (!$this->queryArg) {
            $this->queryArg = $this->name;
        }

        if (!$this->q) {
            $this->q = $this->getQuery();
        }
    }

    /**
     * Return query string sent by request.
     *
     * @return string
     */
    public function getQuery()
    {
        return $_GET[$this->queryArg] ?? null;
    }

    /**
     * Set model condition base on search request.
     */
    public function setModelCondition(Model $m): Model
    {
        if ($q = $this->getQuery()) {
            $m->addCondition('name', 'like', '%' . $q . '%');
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

        $this->js(true, (new jsVueService())->createAtkVue(
            '#' . $this->name,
            'atk-item-search',
            [
                'reload' => $reloadId,
                'queryArg' => $this->queryArg,
                'url' => $this->reload->jsURL(),
                'q' => $this->q,
                'context' => $this->context,
            ]
        ));
    }
}
