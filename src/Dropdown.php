<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Ui\Js\Jquery;
use Atk4\Ui\Js\JsExpression;
use Atk4\Ui\Js\JsExpressionable;
use Atk4\Ui\Js\JsFunction;

class Dropdown extends Lister
{
    public $ui = 'dropdown';

    public $defaultTemplate = 'dropdown.html';

    /** @var JsCallback|null Callback when a new value is selected in Dropdown. */
    public $cb;

    /** @var array As per Fomantic-UI dropdown options. */
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
     *      $dropdown->onChange(function (string $item) {
     *          return 'New selected item: ' . $item;
     *      });.
     *
     * @param \Closure(string): (JsExpressionable|View|string|void) $fx handler where new selected Item value is passed too
     */
    public function onChange(\Closure $fx): void
    {
        // setting dropdown option for using callback URL
        $this->dropdownOptions['onChange'] = new JsFunction(['value', 'name', 't'], [
            new JsExpression(
                'if ($(this).data(\'currentValue\') != value) { $(this).atkAjaxec({ url: [url], urlOptions: { item: value } }); $(this).data(\'currentValue\', value); }',
                ['url' => $this->cb->getJsUrl()]
            ),
        ]);

        $this->cb->set(static function (Jquery $j, string $value) use ($fx) {
            return $fx($value);
        }, ['item' => 'value']);
    }

    protected function renderView(): void
    {
        $this->js(true)->dropdown($this->dropdownOptions);

        parent::renderView();
    }
}
