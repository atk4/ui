<?php

namespace atk4\ui;

class DropDown extends Lister
{
    public $ui = 'dropdown';

    public $defaultTemplate = 'dropdown.html';

    /**
     * Callback when a new value is selected in Dropdown.
     *
     * @var null|jsCallback
     */
    public $cb = null;

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
            $this->cb = jsCallback::addTo($this, ['postTrigger' => 'item']);
        }
    }

    /**
     * Handle callback when user select a new item value in dropdown.
     * Callback is fire only when selecting a different item value then the current item value.
     * ex:
     *      $d = DropDown::addTo($m, ['menu', 'js' => ['on' => 'hover']]);
     *      $d->setModel($menuItems);
     *      $d->onChange(function($item) {
     *          return 'New seleced item: '.$item;
     *      });.
     *
     * @param callable $fx The Handler function where new selected Item value is passed too.
     *
     * @throws Exception
     */
    public function onChange($fx)
    {
        if (!is_callable($fx)) {
            throw new Exception('Error: onChange require a callable function.');
        }
        // setting dropdown option for using callback url.
        $this->js['onChange'] = new jsFunction(['name', 'value', 't'], [
            new jsExpression(
                "if($(this).data('currentValue') != value){\$(this).atkAjaxec({uri:[uri], uri_options:{item:value}});$(this).data('currentValue', value)}",
                ['uri'=> $this->cb->getJSURL()]
            ), ]);

        $this->cb->set(function ($j, $item) use ($fx) {
            return call_user_func($fx, $item);
        }, ['item' => 'value']);
    }

    public function renderView()
    {
        if (isset($this->js)) {
            $this->js(true)->dropdown($this->js);
        } else {
            $this->js(true)->dropdown();
        }

        return parent::renderView();
    }
}
