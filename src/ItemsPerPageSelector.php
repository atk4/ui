<?php

namespace atk4\ui;

/**
 * Implement an item per page length selector.
 * Set as a dropdown menu which contains the number of items per page need.
 */
class ItemsPerPageSelector extends View
{
    public $defaultTemplate = 'pagelength.html';
    public $ui = ' ';

    /**
     * Default page length menu items.
     *
     * @var array
     */
    public $pageLengthItems = [10, 25, 50, 100];

    /**
     * Default button label.
     *  - [ipp] will be replace by the number of pages selected.
     *
     * @var string
     */
    public $label = 'Items per page:';

    /**
     * The current number of item per page.
     *
     * @var int
     */
    public $currentIpp = null;

    /**
     * The callback function.
     *
     * @var Callback|null
     */
    public $cb = null;

    public function init()
    {
        parent::init();

        Icon::addTo($this)->set('dropdown');
        $this->template->tryset('Label', $this->label);

        //Callback later will give us time to properly render menu item before final output.
        $this->cb = CallbackLater::addTo($this);

        if (!$this->currentIpp) {
            $this->currentIpp = $this->pageLengthItems[0];
        }
        $this->set($this->currentIpp);
    }

    /**
     * Set label using js action.
     *
     * @return jQuery
     */
    public function jsSetLabel($ipp)
    {
        return $this->js(true)->html($ipp);
    }

    /**
     * Run callback when an item is select via dropdown menu.
     * The callback should return a View to be reload after an item
     * has been select.
     *
     * @param callable $fx
     */
    public function onPageLengthSelect($fx = null)
    {
        if (is_callable($fx)) {
            if ($this->cb->triggered()) {
                $this->cb->set(function () use ($fx) {
                    $ipp = $_GET['ipp'] ?? null;
                    //$this->pageLength->set(preg_replace("/\[ipp\]/", $ipp, $this->label));
                    $this->set($ipp);
                    $reload = call_user_func($fx, $ipp);
                    if ($reload) {
                        $this->app->terminate($reload->renderJSON());
                    }
                });
            }
        }
    }

    public function renderView()
    {
        $menuItems = [];
        foreach ($this->pageLengthItems as $key => $item) {
            $menuItems[] = ['name' => $item, 'value' => $item];
        }
        //set semantic-ui dropdown onChange function.
        $function = "function(value, text, item){
                            if (value === undefined || value === '' || value === null) return;
                            $(this)
                            .api({
                                on:'now',
                                url:'{$this->cb->getURL()}',
                                data:{ipp:value}
                                }
                            );
                     }";

        $this->js(true)->dropdown([
            'values'   => $menuItems,
            'onChange' => new jsExpression($function),
        ]);
        parent::renderView();
    }
}
