<?php

declare(strict_types=1);

namespace Atk4\Ui;

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
    public $currentIpp;

    /**
     * The callback function.
     *
     * @var Callback|null
     */
    public $cb;

    protected function init(): void
    {
        parent::init();

        Icon::addTo($this)->set('dropdown');
        $this->template->tryset('Label', $this->label);

        // Callback later will give us time to properly render menu item before final output.
        $this->cb = CallbackLater::addTo($this);

        if (!$this->currentIpp) {
            $this->currentIpp = $this->pageLengthItems[0];
        }
        $this->set($this->currentIpp);
    }

    /**
     * Set label using js action.
     *
     * @return Jquery
     */
    public function jsSetLabel($ipp)
    {
        return $this->js(true)->html($ipp);
    }

    /**
     * Run callback when an item is select via dropdown menu.
     * The callback should return a View to be reload after an item
     * has been select.
     */
    public function onPageLengthSelect(\Closure $fx)
    {
        $this->cb->set(function () use ($fx) {
            $ipp = isset($_GET['ipp']) ? (int) $_GET['ipp'] : null;
            //$this->pageLength->set(preg_replace("/\[ipp\]/", $ipp, $this->label));
            $this->set($ipp);
            $reload = $fx($ipp);
            if ($reload) {
                $this->getApp()->terminateJson($reload);
            }
        });
    }

    protected function renderView(): void
    {
        $menuItems = [];
        foreach ($this->pageLengthItems as $key => $item) {
            $menuItems[] = ['name' => $item, 'value' => $item];
        }
        // set semantic-ui dropdown onChange function.
        $function = "function(value, text, item){
                            if (value === undefined || value === '' || value === null) return;
                            $(this)
                            .api({
                                on:'now',
                                url:'{$this->cb->getUrl()}',
                                data:{ipp:value}
                                }
                            );
                     }";

        $this->js(true)->dropdown([
            'values' => $menuItems,
            'onChange' => new JsExpression($function),
        ]);
        parent::renderView();
    }
}
