<?php

namespace atk4\ui\FormLayout;

use atk4\ui\Exception;
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
     * Places field inside a layout somewhere. Should be called
     * through $form->addField().
     *
     * @param string|null              $name
     * @param array|string|object|null $decorator
     * @param array|string|object|null $field
     *
     * @return \atk4\ui\FormField\Generic
     */
    public function addField($name, $decorator = null, $field = null)
    {
        if (!is_string($name)) {
            throw new Exception(['Format for addField now require first argument to be name']);
        }

        if (!$this->form->model) {
            $this->form->model = new \atk4\ui\misc\ProxyModel();
        }

        if (is_string($field)) {
            $field = ['type' => $field];
        }

        if ($name) {
            $existingField = $this->form->model->hasElement($name);
        }

        try {
            if (!$existingField) {
                // Add missing field
                if ($field) {
                    $field = $this->form->model->addField($name, $field);
                } else {
                    $field = $this->form->model->addField($name);
                }
            } elseif (is_array($field)) {
                // Add properties to existing field
                $existingField->setDefaults($field);
                $field = $existingField;
            } elseif (is_object($field)) {
                throw new Exception(['Duplicate field', 'name' => $name]);
            } else {
                $field = $existingField;
            }

            if (is_string($decorator)) {
                $decorator = $this->form->decoratorFactory($field, ['caption' => $decorator]);
            } elseif (is_array($decorator)) {
                $decorator = $this->form->decoratorFactory($field, $decorator);
            } elseif (!$decorator) {
                $decorator = $this->form->decoratorFactory($field);
            } elseif (is_object($decorator)) {
                if (!$decorator instanceof \atk4\ui\FormField\Generic) {
                    throw new Exception(['Field decorator must descend from \atk4\ui\FormField\Generic', 'decorator' => $decorator]);
                }
                $decorator->field = $field;
                $decorator->form = $this->form;
            } else {
                throw new Exception(['Value of $decorator argument is incorrect', 'decorator' => $decorator]);
            }
        } catch (\Throwable $e) {
            throw new Exception(['Unable to add form field', 'name' => $name, 'decorator' => $decorator, 'field' => $field], null, $e);
        }

        return $this->_add($decorator, ['desired_name' => $field->short_name]);
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
                $this->addField($field);
            }
        } else {
            throw new Exception(['Incorrect value for $fields', 'fields' => $fields]);
        }

        return $model;
    }

    /**
     * Adds Button.
     *
     * @param array|string $button
     *
     * @return \atk4\ui\Button
     */
    public function addButton($button)
    {
        if (is_array($button)) {
            array_unshift($button, 'Button');
        } elseif (is_string($button)) {
            $button = ['Button', $button];
        }

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
            $this->add(['Header', $label, 'dividing', 'element' => 'h4']);
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
            $label = ['label' => $label];
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

            // Anything but fields or explicitly defined fields gets inserted directly
            if (!$el instanceof \atk4\ui\FormField\Generic || !$el->layoutWrap) {
                $this->template->appendHTML('Content', $el->getHTML());
                continue;
            }

            $template = $field_input;
            $label = $el->caption ?: $el->field->getCaption();

            // Anything but fields gets inserted directly
            if ($el instanceof \atk4\ui\FormField\CheckBox) {
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

            if (isset($el->width)) {
                $template->append('field_class', $el->width.' wide ');
            }

            if ($el->hint) {
                $hint = new \atk4\ui\Label([null, 'pointing', 'id'=>$el->id.'_hint']);
                if (is_object($el->hint) || is_array($el->hint)) {
                    $hint->add($el->hint);
                } else {
                    $hint->set($el->hint);
                }
                $template->setHTML('Hint', $hint->getHTML());
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
