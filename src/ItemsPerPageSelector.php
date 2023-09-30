<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Data\Field;
use Atk4\Ui\Js\JsExpression;

/**
 * Implement an item per page length selector.
 * Set as a dropdown menu which contains the number of items per page need.
 */
class ItemsPerPageSelector extends View
{
    public $defaultTemplate = 'pagelength.html';
    public $ui = 'selection compact dropdown';

    /** @var list<int> Default page length menu items. */
    public $pageLengthItems = [10, 100, 1000];

    /** @var string */
    public $label = 'Items per page:';

    /** @var int The current number of item per page. */
    public $currentIpp;

    /** @var Callback|null The callback function. */
    public $cb;

    private function formatInteger(int $value): string
    {
        return $this->getApp()->uiPersistence->typecastSaveField(new Field(['type' => 'integer']), $value);
    }

    protected function init(): void
    {
        parent::init();

        Icon::addTo($this)->set('dropdown');
        $this->template->trySet('Label', $this->label);

        // CallbackLater will give us time to properly render menu item before final output
        $this->cb = CallbackLater::addTo($this);

        if (!$this->currentIpp) {
            $this->currentIpp = $this->pageLengthItems[0];
        }
        $this->set($this->formatInteger($this->currentIpp));
    }

    /**
     * Run callback when an item is select via dropdown menu.
     * The callback should return a View to be reloaded after an item
     * has been select.
     *
     * @param \Closure(int): (View|void) $fx
     */
    public function onPageLengthSelect(\Closure $fx): void
    {
        $this->cb->set(function () use ($fx) {
            $ipp = $this->getApp()->hasRequestQueryParam('ipp') ? (int) $this->getApp()->getRequestQueryParam('ipp') : null;
            $this->set($this->formatInteger($ipp));
            $reload = $fx($ipp);
            if ($reload) {
                $this->getApp()->terminateJson($reload);
            }
        });
    }

    protected function renderView(): void
    {
        $menuItems = [];
        foreach ($this->pageLengthItems as $item) {
            $menuItems[] = ['name' => $this->formatInteger($item), 'value' => $item];
        }

        $function = new JsExpression('function (value, text, item) {
            if (value === undefined || value === \'\' || value === null) {
                return;
            }
            $(this).api({
                on: \'now\',
                url: \'' . $this->cb->getUrl() . '\',
                data: {ipp:value}
            });
        }');

        $this->js(true)->dropdown([
            'values' => $menuItems,
            'onChange' => $function,
        ]);

        parent::renderView();
    }
}
