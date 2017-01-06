<?php

namespace atk4\ui\FormLayout;

use atk4\ui\Form;
use atk4\ui\View;

/**
 * Generic Layout for a form
 */
class Vertical extends View
{
    public $form = null;

    public $defaultTemplate = 'formlayout/vertical.html';

    /**
     * Places field inside a layout somewhere
     */
    function addField(\atk4\ui\FormField\Generic $field)
    {
        return $this->_add($field, ['name'=>$field->short_name]);
    }

    function addButton(\atk4\ui\Button $button) {
        return $this->_add($button);
    }

    /**
     * Create a group with fields
     */
    function addGroup($label = null)
    {
        if ($label) {
            $this->add(new View([$label, 'ui'=>'dividing header', 'element'=>'h4']));
        }
    }

    function recursiveRender() {
        $field_input = $this->template->cloneRegion('InputField');
        $field_checkbox = $this->template->cloneRegion('InputCheckbox');

        $this->template->del('Fields');

        foreach($this->elements as $el) {

            // Buttons go under Button section
            if ($el instanceof \atk4\ui\Button) {
                $this->template->appendHTML('Buttons', $el->getHTML());
                continue;
            }

            // Anything but fields gets inserted directly
            if (!$el instanceof \atk4\ui\FormField\Generic) {
                $this->template->appendHTML('Fields', $el->getHTML());
                continue;
            }

            $template = $field_input;
            $label = isset($el->field->ui['caption'])?
                $el->field->ui['caption']:ucwords(str_replace('_',' ', $el->field->short_name));


            // Anything but fields gets inserted directly
            if ($el instanceof \atk4\ui\FormField\Checkbox) {
                $template = $field_checkbox;
                $el->set($label);
                /*
                $el->addClass('field');
                $this->template->appendHTML('Fields', '<div class="field">'.$el->getHTML().'</div>');
                continue;
                 */
            }

            // Fields get extra pampering
            $template->setHTML('Input', $el->getHTML());
            $template->trySet('label', $label);

            $this->template->appendHTML('Fields', $template->render());
        }

        // Now collect JS from everywhere
        foreach($this->elements as $el){
            if ($el->_js_actions) {
                $this->_js_actions = array_merge_recursive($this->_js_actions, $el->_js_actions);
            }
        }
    }
}
