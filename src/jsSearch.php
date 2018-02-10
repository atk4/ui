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
    public $input = null;

    public function init()
    {
        parent::init();

        $this->input = $this->add(new \atk4\ui\FormField\Line(['action' => new \atk4\ui\Button(['icon' => 'search', 'ui' => 'button atk-action'])]));
    }

    public function renderView()
    {
        $this->js(true)->atkJsSearch(['uri' => $this->reload->url(), 'uri_options' => ['__atk_reload'=>$this->reload->name]]);
        parent::renderView();
    }
}
