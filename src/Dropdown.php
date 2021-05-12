<?php

declare(strict_types=1);

namespace Atk4\Ui;

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
     * As per Fomantic-ui dropdown options.
     *
     * @var array
     */
    public $dropdownOptions = [];

    protected function init(): void
    {
        parent::init();

        if (!$this->cb) {
            $this->cb = JsCallback::addTo($this);
        }
    }

    /**
     * Handle callback when user select a new item value in dropdown.
     * Callback is fire only when selecting a different item value then the current item value.
     * ex:
     *      $dropdown = Dropdown::addTo($menu, ['menu', 'dropdownOptions' => ['on' => 'hover']]);
     *      $dropdown->setModel($menuItems);
     *      $dropdown->onChange(function($item) {
     *          return 'New selected item: '.$item;
     *      });.
     *
     * @param \Closure $fx handler where new selected Item value is passed too
     */
    public function onChange(\Closure $fx)
    {
        // setting dropdown option for using callback url.
        $this->dropdownOptions['onChange'] = new JsFunction(['value', 'name', 't'], [
            new JsExpression(
                "if($(this).data('currentValue') != value){\$(this).atkAjaxec({uri:[uri], uri_options:{item:value}});$(this).data('currentValue', value)}",
                ['uri' => $this->cb->getJsUrl()]
            ), ]);

        $this->cb->set(function ($j, $value) use ($fx) {
            return $fx($value);
        }, ['item' => 'value']);
    }

    protected function renderView(): void
    {
        $this->js(true)->dropdown($this->dropdownOptions);

        parent::renderView();
    }
}
