<?php

namespace atk4\ui\FormLayout;

use atk4\ui\Form;
use atk4\ui\View;

/**
 * Generic Layout for a form.
 */
class Vertical extends View
{
    public $form = null;

    public $defaultTemplate = 'formlayout/vertical.html';

    /**
     * Places field inside a layout somewhere.
     */
    public function addField(\atk4\ui\FormField\Generic $field)
    {
        return $this->_add($field, ['name'=>$field->short_name]);
    }

    public function addButton(\atk4\ui\Button $button)
    {
        return $this->_add($button);
    }

    public function recursiveRender()
    {
        $t_field = $this->template->cloneRegion('Field');
        $this->template->del('Fields');

        foreach ($this->elements as $el) {
            if ($el instanceof \atk4\ui\Button) {
                $this->template->appendHTML('Buttons', $el->getHTML());
                if ($el->_js_actions) {
                    $this->_js_actions = array_merge_recursive($this->_js_actions, $el->_js_actions);
                }
                continue;
            }

            $t_field->setHTML('Input', $el->getHTML());
            $t_field->set('label',
                isset($el->field->ui['caption']) ?
                $el->field->ui['caption'] : ucwords(str_replace('_', ' ', $el->field->short_name))

            );
            if ($el->_js_actions) {
                $this->_js_actions = array_merge_recursive($this->_js_actions, $el->_js_actions);
            }

            $this->template->appendHTML('Fields', $t_field->render());
        }
    }
}
