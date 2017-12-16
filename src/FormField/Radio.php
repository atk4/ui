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
     * Contains a lister that will render individual radio buttons
     */
    public $lister = null;

    /**
     * List of values
     */
    public $values = [];

    public function init()
    {
        parent::init();
        $this->lister = $this->add('Lister', 'Radio');
        $this->lister->t_row['_name'] = $this->short_name;
    }

    public function renderView()
    {

        if (!$this->model) {
            $p = new \atk4\data\Persistence_Static($this->values);
            $m = new \atk4\data\Model($p);
        }

        $value = $this->field ? $this->field->get() : $this->content;

        $this->lister->setModel($m);
        $this->lister->addHook('beforeRow', function($lister) use($value) {
            $lister->t_row->set('checked', $value == $lister->model->id ? 'checked':'');
        });

        return parent::renderView();
    }
}
