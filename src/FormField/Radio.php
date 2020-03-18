<?php

namespace atk4\ui\FormField;

/**
 * Input element for a form field.
 */
class Radio extends Generic
{
    public $ui = false;

    public $defaultTemplate = 'formfield/radio.html';

    /**
     * Contains a lister that will render individual radio buttons.
     *
     * @var Lister
     */
    public $lister = null;

    /**
     * List of values.
     *
     * @var array
     */
    public $values = [];

    /**
     * Initialization.
     */
    public function init()
    {
        parent::init();

        $this->lister = $this->add('Lister', 'Radio');
        $this->lister->t_row['_name'] = $this->short_name;
    }

    /**
     * Renders view.
     */
    public function renderView()
    {
        if (!$this->model) {
            $p = new \atk4\data\Persistence\Static_($this->values);
            $this->setModel(new \atk4\data\Model($p));
        }

        $value = $this->field ? $this->field->get() : $this->content;

        $this->lister->setModel($this->model);

        // take care of readonly and disabled statuses
        if ($this->disabled) {
            $this->addClass('disabled');
        }

        $this->lister->onHook('beforeRow', function ($lister) use ($value) {
            if ($this->readonly) {
                $lister->t_row->set('disabled', $value != $lister->model->id ? 'disabled="disabled"' : '');
            } elseif ($this->disabled) {
                $lister->t_row->set('disabled', 'disabled="disabled"');
            }

            $lister->t_row->set('checked', $value == $lister->model->id ? 'checked' : '');
        });

        return parent::renderView();
    }

    /**
     * Shorthand method for on('change') event.
     * Some input fields, like Calendar, could call this differently.
     *
     * If $expr is string or jsExpression, then it will execute it instantly.
     * If $expr is callback method, then it'll make additional request to webserver.
     *
     * Examples:
     * $field->onChange('console.log("changed")');
     * $field->onChange(new \atk4\ui\jsExpression('console.log("changed")'));
     * $field->onChange('$(this).parents(".form").form("submit")');
     *
     * @param string|jsExpression|array|callable $expr
     * @param array|bool                         $default
     */
    public function onChange($expr, $default = [])
    {
        if (is_string($expr)) {
            $expr = new \atk4\ui\jsExpression($expr);
        }

        if (is_bool($default)) {
            $default['preventDefault'] = $default;
            $default['stopPropagation'] = $default;
        }

        $this->on('change', 'input', $expr, $default);
    }
}
