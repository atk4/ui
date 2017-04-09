<?php

namespace atk4\ui\FormLayout;

use atk4\ui\Form;
use atk4\ui\View;

/**
 * Generic Layout for a form.
 */
class Generic extends View
{
    /**
     * Links layout to the form.
     */
    public $form = null;

    // @var inheritdoc
    public $defaultTemplate = 'formlayout/generic.html';

    /**
     * If specified will appear on top of the group. Can be string or Label object.
     */
    public $label = null;

    /**
     * Specify width of a group in numerical word e.g. 'width'=>'two' as per
     * Semantic UI grid system.
     */
    public $width = null;

    /**
     * Set true if you want fields to appear in-line.
     */
    public $inline = null;

    /**
     * Places field inside a layout somewhere.
     *
     * @param \atk4\ui\FormField\Generic|array $field
     * @param array|string                     $args
     *
     * @return \atk4\ui\FormField\Generic
     */
    public function addField($field, $args = [])
    {
        if (is_string($args)) {
            $args = ['caption' => $args];
        } elseif (is_array($args) && isset($args[0])) {
            $args['caption'] = $args[0];
            unset($args[0]);
        }

        /*
        if (isset($args[1]) && is_string($args[1])) {
            $args[1] = ['ui'=>['caption'=>$args[1]]];
        }
         */

        if (is_array($field)) {
            $field = $this->form->fieldFactory(...$field);
        } elseif (!$field instanceof \atk4\ui\FormField\Generic) {
            $field = $this->form->fieldFactory($field);
        }

        if (isset($args['caption'])) {
            $field->field->caption = $args['caption'];
        }

        if (isset($args['width'])) {
            $field->field->ui['width'] = $args['width'];
        }

        return $this->_add($field, ['name'=>$field->short_name]);
    }

    public function setModel(\atk4\data\Model $model, $fields = null)
    {
        parent::setModel($model);

        if ($fields === false) {
            return $model;
        }

        if ($fields === null) {
            $fields = [];
            foreach ($model->elements as $f) {
                if (!$f instanceof \atk4\data\Field) {
                    continue;
                }

                if (!$f->isEditable()) {
                    continue;
                }
                $fields[] = $f->short_name;
            }
        }

        if (is_array($fields)) {
            foreach ($fields as $field) {
                $modelField = $model->getElement($field);

                $formField = $this->addField($this->form->fieldFactory($modelField));
            }
        } else {
            throw new Exception(['Incorrect value for $fields', 'fields'=>$fields]);
        }

        return $model;
    }

    /**
     * Adds Button.
     *
     * @param \atk4\ui\Button $button
     *
     * @return \atk4\ui\Button
     */
    public function addButton(\atk4\ui\Button $button)
    {
        return $this->_add($button);
    }

    /**
     * Create a group with fields.
     *
     * @param string $label
     *
     * @return $this
     */
    public function addHeader($label = null)
    {
        if ($label) {
            $this->add(new View([$label, 'ui'=>'dividing header', 'element'=>'h4']));
        }

        return $this;
    }

    /**
     * Adds group.
     *
     * @param string|array $label
     *
     * @return self
     */
    public function addGroup($label = null)
    {
        if (!is_array($label)) {
            $label = ['label'=>$label];
        } elseif (isset($label[0])) {
            $label['label'] = $label[0];
            unset($label[0]);
        }

        $label['form'] = $this->form;

        return $this->add(new self($label));
    }

    public function recursiveRender()
    {
        $field_input = $this->template->cloneRegion('InputField');
        $field_no_label = $this->template->cloneRegion('InputNoLabel');
        $labeled_group = $this->template->cloneRegion('LabeledGroup');
        $no_label_group = $this->template->cloneRegion('NoLabelGroup');

        $this->template->del('Content');

        foreach ($this->elements as $el) {

            // Buttons go under Button section
            if ($el instanceof \atk4\ui\Button) {
                $this->template->appendHTML('Buttons', $el->getHTML());
                continue;
            }

            if ($el instanceof \atk4\ui\FormLayout\Generic) {
                if ($el->label && !$el->inline) {
                    $template = $labeled_group;
                    $template->set('label', $el->label);
                } else {
                    $template = $no_label_group;
                }

                if ($el->width) {
                    $template->set('width', $el->width);
                }

                if ($el->inline) {
                    $template->set('class', 'inline');
                }
                $template->setHTML('Content', $el->getHTML());

                $this->template->appendHTML('Content', $template->render());
                continue;
            }

            // Anything but fields gets inserted directly
            if (!$el instanceof \atk4\ui\FormField\Generic) {
                $this->template->appendHTML('Content', $el->getHTML());
                continue;
            }

            $template = $field_input;
            $label = $el->field->getCaption();

            // Anything but fields gets inserted directly
            if ($el instanceof \atk4\ui\FormField\Checkbox) {
                $template = $field_no_label;
                $el->template->set('Content', $label);
                /*
                $el->addClass('field');
                $this->template->appendHTML('Fields', '<div class="field">'.$el->getHTML().'</div>');
                continue;
                 */
            }

            if ($this->label && $this->inline) {
                $el->placeholder = $label;
                $label = $this->label;
                $this->label = null;
            } elseif ($this->label || $this->inline) {
                $template = $field_no_label;
                $el->placeholder = $label;
            }

            // Fields get extra pampering
            $template->setHTML('Input', $el->getHTML());
            $template->trySet('label', $label);
            $template->trySet('label_for', $el->id.'_input');
            $template->set('field_class', '');

            if ($el->field->required) {
                $template->append('field_class', 'required ');
            }

            if (isset($el->field->ui['width'])) {
                $template->append('field_class', $el->field->ui['width'].' wide ');
            }

            $this->template->appendHTML('Content', $template->render());
        }

        // Now collect JS from everywhere
        foreach ($this->elements as $el) {
            if ($el->_js_actions) {
                $this->_js_actions = array_merge_recursive($this->_js_actions, $el->_js_actions);
            }
        }
    }
}
