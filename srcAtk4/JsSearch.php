<?php

declare(strict_types=1);
/**
 * A Search input field that will reload View
 * using the view->url with a _q arguments attach to url.
 */

namespace Atk4\Ui;

class JsSearch extends View
{
    /**
     * The View to reload using this JsSearch.
     *
     * @var View
     */
    public $reload;

    public $args = [];

    /**
     * Whether or not JsSearch will query server on each keystroke.
     * Default is with using Enter key.
     *
     * @var bool
     */
    public $autoQuery = false;

    /**
     * The input field.
     *
     * @var Form\Control\Line
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
     * @var string
     */
    public $initValue;

    /**
     * Whether or not this search will reload a view
     * or the entire page.
     * If search query need to be control via an url parameter only
     * set this to false.
     *
     * @var bool default to true
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
    public $btnStyle;

    protected function init(): void
    {
        parent::init();

        //$this->input = Form\Control\Line::addTo($this, ['iconLeft' => 'filter',  'action' => new Button(['icon' => 'search', 'ui' => 'button atk-action'])]);
    }

    protected function renderView(): void
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
            'uri' => $this->reload->jsUrl(),
            'uri_options' => array_merge(['__atk_reload' => $this->reload->name], $this->args),
            'autoQuery' => $this->autoQuery,
            'q' => $this->initValue,
            'useAjax' => $this->useAjax,
        ]);

        parent::renderView();
    }
}
