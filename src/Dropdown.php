<?php

declare(strict_types=1);

namespace atk4\ui;

class Dropdown extends Lister
{
    public $ui = 'dropdown';

    public $defaultTemplate = 'dropdown.html';

    /**
     * Callback when a new value is selected in Dropdown.
     *
     * @var JsCallback|null
     */
    public $cb;

    /**
     * Supply an optional parameter to the drop-down.
     *
     * @var array will be converted to json passed into dropdown()
     */
    public $js;

    public function init(): void
    {
        parent::init();

        if (!$this->cb) {
            $this->cb = JsCallback::addTo($this, ['postTrigger' => 'item']);
        }
    }

    /**
     * Handle callback when user select a new item value in dropdown.
     * Callback is fire only when selecting a different item value then the current item value.
     * ex:
     *      $dropdown = Dropdown::addTo($menu, ['menu', 'js' => ['on' => 'hover']]);
     *      $dropdown->setModel($menuItems);
     *      $dropdown->onChange(function($item) {
     *          return 'New seleced item: '.$item;
     *      });.
     *
     * @param \Closure $fx handler where new selected Item value is passed too
     */
    public function onChange(\Closure $fx)
    {
        // setting dropdown option for using callback url.
        $this->js['onChange'] = new JsFunction(['name', 'value', 't'], [
            new JsExpression(
                "if($(this).data('currentValue') != value){\$(this).atkAjaxec({uri:[uri], uri_options:{item:value}});$(this).data('currentValue', value)}",
                ['uri' => $this->cb->getJsUrl()]
            ), ]);

        $this->cb->set(function ($j, $item) use ($fx) {
            return call_user_func($fx, $item);
        }, ['item' => 'value']);
    }

    protected function renderView(): void
    {
        if (isset($this->js)) {
            $this->js(true)->dropdown($this->js);
        } else {
            $this->js(true)->dropdown();
        }

        parent::renderView();
    }
}
