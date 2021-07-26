<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Control;

use Atk4\Ui\Form;
use Atk4\Ui\Lister;

/**
 * Input element for a form control.
 */
class Radio extends Form\Control
{
    public $ui = false;

    public $defaultTemplate = 'form/control/radio.html';

    /**
     * Contains a lister that will render individual radio buttons.
     *
     * @var Lister
     */
    public $lister;

    /**
     * List of values.
     *
     * @var array
     */
    public $values = [];

    /**
     * Initialization.
     */
    protected function init(): void
    {
        parent::init();

        $this->lister = Lister::addTo($this, [], ['Radio']);
        $this->lister->t_row->set('_name', $this->short_name);
    }

    protected function renderView(): void
    {
        if (!$this->model) {
            $this->setSource($this->values);
        }

        $value = $this->field ? $this->field->get() : $this->content;

        $this->lister->setModel($this->model);

        // take care of readonly and disabled statuses
        if ($this->disabled) {
            $this->addClass('disabled');
        }

        $this->lister->onHook(Lister::HOOK_BEFORE_ROW, function (Lister $lister) use ($value) {
            if ($this->readonly) {
                $lister->t_row->set('disabled', $value !== (string) $lister->model->getId() ? 'disabled="disabled"' : '');
            } elseif ($this->disabled) {
                $lister->t_row->set('disabled', 'disabled="disabled"');
            }

            $lister->t_row->set('checked', $value === (string) $lister->model->getId() ? 'checked' : '');
        });

        parent::renderView();
    }

    /**
     * Shorthand method for on('change') event.
     * Some input fields, like Calendar, could call this differently.
     *
     * If $expr is string or JsExpression, then it will execute it instantly.
     * If $expr is callback method, then it'll make additional request to webserver.
     *
     * Examples:
     * $control->onChange('console.log("changed")');
     * $control->onChange(new \Atk4\Ui\JsExpression('console.log("changed")'));
     * $control->onChange('$(this).parents(".form").form("submit")');
     *
     * @param string|\Atk4\Ui\JsExpression|array|\Closure $expr
     * @param array|bool                                  $default
     */
    public function onChange($expr, $default = [])
    {
        if (is_string($expr)) {
            $expr = new \Atk4\Ui\JsExpression($expr);
        }

        if (is_bool($default)) {
            $default['preventDefault'] = $default;
            $default['stopPropagation'] = $default;
        }

        $this->on('change', 'input', $expr, $default);
    }
}
