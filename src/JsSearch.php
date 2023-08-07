<?php

declare(strict_types=1);

namespace Atk4\Ui;

/**
 * A Search input field that will reload View
 * using the view->url with a _q arguments attach to URL.
 */
class JsSearch extends View
{
    public $ui = 'left icon action transparent input';
    public $defaultTemplate = 'js-search.html';

    /** @var View The View to reload using this JsSearch. */
    public $reload;

    /** @var array */
    public $args = [];

    /**
     * Whether or not JsSearch will query server on each keystroke.
     * Default is with using Enter key.
     *
     * @var bool
     */
    public $autoQuery = false;

    /** @var Form\Control\Line|null The input field. */
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
     * If search query need to be control via an URL parameter only
     * set this to false.
     *
     * @var bool
     */
    public $useAjax = true;

    /** @var string ui CSS classes */
    public $button = 'ui mini transparent basic button';
    /** @var string */
    public $filterIcon = 'filter';
    /** @var string */
    public $buttonSearchIcon = 'search';
    /** @var string */
    public $buttonRemoveIcon = 'red remove';
    /** @var string|null */
    public $buttonStyle;

    protected function init(): void
    {
        parent::init();

        // $this->input = Form\Control\Line::addTo($this, ['iconLeft' => 'filter', 'action' => new Button(['icon' => 'search', 'ui' => 'button atk-action'])]);
    }

    protected function renderView(): void
    {
        if ($this->placeHolder) {
            $this->template->trySet('Placeholder', $this->placeHolder);
        }

        if ($this->buttonStyle) {
            $this->template->trySet('buttonStyle', $this->buttonStyle);
        }

        $this->template->set('Button', $this->button);
        $this->template->set('FilterIcon', $this->filterIcon);
        $this->template->set('ButtonSearchIcon', $this->buttonSearchIcon);
        $this->template->set('ButtonRemoveIcon', $this->buttonRemoveIcon);

        $this->js(true)->atkJsSearch([
            'url' => $this->reload->jsUrl(),
            'urlOptions' => array_merge(['__atk_reload' => $this->reload->name], $this->args),
            'urlQueryKey' => $this->name . '_q',
            'autoQuery' => $this->autoQuery,
            'q' => $this->initValue,
            'useAjax' => $this->useAjax,
        ]);

        parent::renderView();
    }
}
