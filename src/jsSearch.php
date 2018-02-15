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

    /**
     * The input field.
     *
     * @var FormField\Line
     */
    public $placeHolder = 'Search';

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

        //$this->input = $this->add(new \atk4\ui\FormField\Line(['iconLeft' => 'filter',  'action' => new \atk4\ui\Button(['icon' => 'search', 'ui' => 'button atk-action'])]));
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

        $this->js(true)->atkJsSearch(['uri' => $this->reload->url(), 'uri_options' => ['__atk_reload'=>$this->reload->name]]);
        parent::renderView();
    }
}
