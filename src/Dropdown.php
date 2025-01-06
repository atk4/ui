<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Ui\Js\Jquery;
use Atk4\Ui\Js\JsCallbackLoadableValue;
use Atk4\Ui\Js\JsExpression;
use Atk4\Ui\Js\JsExpressionable;
use Atk4\Ui\Js\JsFunction;

class Dropdown extends Lister
{
    public $ui = 'dropdown';

    public $defaultTemplate = 'dropdown.html';

    /** @var JsCallback|null Callback when a new value is selected in Dropdown. */
    public $cb;

    /** @var string|null Set static contents of this view. */
    public $content;

    /** @var array<string, mixed> As per Fomantic-UI dropdown options. */
    public $dropdownOptions = [];

    /**
     * @param array<0|string, mixed>|string $label
     */
    public function __construct($label = [])
    {
        $defaults = is_array($label) ? $label : [$label];

        if (array_key_exists(0, $defaults)) {
            $defaults['content'] = $defaults[0];
            unset($defaults[0]);
        }

        parent::__construct($defaults);
    }

    #[\Override]
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
     *      $dropdown->onChange(function ($id) {
     *          return 'New selected item: ' . $id;
     *      });.
     *
     * @param \Closure(mixed): (JsExpressionable|View|string|void) $fx handler where new selected Item value is passed to
     */
    public function onChange(\Closure $fx): void
    {
        // setting dropdown option for using callback URL
        $this->dropdownOptions['onChange'] = new JsFunction(['value', 'name', 't'], [
            new JsExpression(
                'if ($(this).data(\'currentValue\') != value) { $(this).atkAjaxExecute({ url: [url], urlOptions: { item: value } }); $(this).data(\'currentValue\', value); }',
                ['url' => $this->cb->getJsUrl()]
            ),
        ]);

        $this->cb->set(static function (Jquery $j, $value) use ($fx) {
            return $fx($value);
        }, ['item' => new JsCallbackLoadableValue(null, function ($v) {
            return $this->getApp()->uiPersistence->typecastAttributeLoadField(
                $this->model->getIdField(),
                $v
            );
        })]);
    }

    #[\Override]
    protected function renderView(): void
    {
        $this->js(true)->dropdown($this->dropdownOptions);

        parent::renderView();

        if ($this->content !== null) {
            $this->template->append('Content', $this->content);
        }
    }

    #[\Override]
    protected function renderTRow(): void
    {
        $this->tRow->set('id', $this->getApp()->uiPersistence->typecastAttributeSaveField($this->model->getIdField(), $this->currentRow->getId()));
        $this->tRow->set('name', $this->getApp()->uiPersistence->typecastSaveField($this->model->getField($this->model->titleField), $this->currentRow->getTitle()));
    }
}
