<?php
/**
 * A Search input field that will reload View
 * using the view->url with a _q arguments attach to url.
 */

namespace atk4\ui;

class jsSearch extends View
{
    /**
     * The View to reload using this jsSearch.
     *
     * @var View
     */
    public $reload = null;

    public $args = [];

    /**
     * Whether or not jsSearch will query server on each keystroke.
     * Default is with using Enter key.
     *
     * @var bool
     */
    public $autoQuery = false;

    /**
     * The input field.
     *
     * @var FormField\Line
     */
    public $placeHolder = 'Search';

    /**
     * The initial value to display in search
     * input field.
     * Atn: This is not reloading the view but
     * rather display in initial input value.
     * Make sure the model results set match the initial value.
     * Mostly use when not using ajax reload.
     *
     * @var null
     */
    public $initValue = null;

    /**
     * Whether or not this search will reload a view
     * or the entire page.
     * If search query need to be control via an url parameter only
     * set this to false.
     *
     * @var bool Default to true.
     */
    public $useAjax = true;

    public function link($url, $target = null)
    {
        return parent::link($url, $target);
    }

    public $defaultTemplate = 'js-search.html';

    /** @var string ui css classes */
    public $button = 'ui mini transparent basic button';
    public $filterIcon = 'filter';
    public $btnSearchIcon = 'search';
    public $btnRemoveIcon = 'red remove';
    public $btnStyle = null;

    public function init()
    {
        parent::init();

        //$this->input = FormField\Line::addTo($this, ['iconLeft' => 'filter',  'action' => new \atk4\ui\Button(['icon' => 'search', 'ui' => 'button atk-action'])]);
    }

    public function renderView()
    {
        if ($this->placeHolder) {
            $this->template->trySet('Placeholder', $this->placeHolder);
        }

        if ($this->btnStyle) {
            $this->template->trySet('button_style', $this->btnStyle);
        }

        $this->template->set('Button', $this->button);
        $this->template->set('FilterIcon', $this->filterIcon);
        $this->template->set('BtnSearchIcon', $this->btnSearchIcon);
        $this->template->set('BtnRemoveIcon', $this->btnRemoveIcon);

        $this->js(true)->atkJsSearch([
            'uri'         => $this->reload->jsURL(),
            'uri_options' => array_merge(['__atk_reload'=>$this->reload->name], $this->args),
            'autoQuery'   => $this->autoQuery,
            'q'           => $this->initValue,
            'useAjax'     => $this->useAjax,
        ]);

        parent::renderView();
    }
}
