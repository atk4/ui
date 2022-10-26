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

    /** @var array Default page length menu items. */
    public $pageLengthItems = [10, 25, 50, 100];

    /**
     * Default button label.
     *  - [ipp] will be replace by the number of pages selected.
     *
     * @var string
     */
    public $label = 'Items per page:';

    /** @var int The current number of item per page. */
    public $currentIpp;

    /** @var Callback|null The callback function. */
    public $cb;

    protected function init(): void
    {
        parent::init();

        Icon::addTo($this)->set('dropdown');
        $this->template->trySet('Label', $this->label);

        // Callback later will give us time to properly render menu item before final output.
        $this->cb = CallbackLater::addTo($this);

        if (!$this->currentIpp) {
            $this->currentIpp = $this->pageLengthItems[0];
        }
        $this->set($this->currentIpp);
    }

    /**
     * Run callback when an item is select via dropdown menu.
     * The callback should return a View to be reloaded after an item
     * has been select.
     */
    public function onPageLengthSelect(\Closure $fx): void
    {
        $this->cb->set(function () use ($fx) {
            $ipp = isset($_GET['ipp']) ? (int) $_GET['ipp'] : null;
            // $this->pageLength->set(preg_replace("/\[ipp\]/", $ipp, $this->label));
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
        // set Fomantic-UI dropdown onChange function.
        $function = 'function (value, text, item) {
            if (value === undefined || value === \'\' || value === null) return;
            $(this)
            .api({
                on:\'now\',
                url:\'' . $this->cb->getUrl() . '\',
                data:{ipp:value}
            });
        }';

        $this->js(true)->dropdown([
            'values' => $menuItems,
            'onChange' => new JsExpression($function),
        ]);

        parent::renderView();
    }
}
