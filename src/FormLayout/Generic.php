<?php

namespace atk4\ui\FormLayout;

use atk4\ui\Form;
use atk4\ui\Label;
use atk4\ui\Template;

/**
 * Generic Layout for a form.
 */
class Generic extends _Abstract
{
    /** {@inheritdoc} */
    public $defaultTemplate = 'formlayout/generic.html';

    /** @var string Default input template file. */
    public $defaultInputTemplate = 'formlayout/generic-input.html';

    /**
     * If specified will appear on top of the group. Can be string or Label object.
     *
     * @var string
     */
    public $label;

    /**
     * Specify width of a group in numerical word e.g. 'width'=>'two' as per
     * Semantic UI grid system.
     *
     * @var string
     */
    public $width;

    /**
     * Set true if you want fields to appear in-line.
     *
     * @var bool
     */
    public $inline = false;

    /** @var Template Template holding input html. */
    public $inputTemplate;

    /** @var array Seed for creating input hint View used in this layout. */
    public $defaultHint = [Label::class, 'class' => ['pointing']];

    protected function _addField($decorator, $field)
    {
        return $this->_add($decorator, ['desired_name' => $field->short_name]);
    }

    public function init(): void
    {
        parent::init();

        if (!$this->inputTemplate) {
            $this->inputTemplate = $this->app->loadTemplate($this->defaultInputTemplate);
        }
    }

    /**
     * Adds Button.
     *
     * @param Button|array|string $seed
     *
     * @return \atk4\ui\Button
     */
    public function addButton($seed)
    {
        return $this->add($this->mergeSeeds(['Button'], $seed), 'Buttons');
    }

    /**
     * Adds Header in form layout.
     *
     * @param string $label
     *
     * @return $this
     */
    public function addHeader($label)
    {
        \atk4\ui\Header::addTo($this, [$label, 'dividing', 'element' => 'h4']);

        return $this;
    }

    /**
     * Adds field group in form layout.
     *
     * @param string|array $label
     *
     * @return static
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

        return static::addTo($this, [$label]);
    }

    /**
     * Add a form layout section to this layout.
     *
     * Each section may contain other section or group.
     *
     * @param mixed $seed
     * @param bool  $addDivider Should we add divider after this section
     *
     * @throws \atk4\core\Exception
     * @throws \atk4\ui\Exception
     *
     * @return static
     */
    public function addSubLayout($seed = self::class, $addDivider = true)
    {
        $v = $this->add($this->factory($seed, ['form' => $this->form]));
        if ($v instanceof \atk4\ui\FormLayout\Section\Generic) {
            $v = $v->addSection();
        }

        if ($addDivider) {
            \atk4\ui\View::addTo($this, ['ui' => 'hidden divider']);
        }

        return $v;
    }

    /**
     * Recursively renders this view.
     */
    public function recursiveRender()
    {
        $field_input = $this->inputTemplate->cloneRegion('InputField');
        $field_no_label = $this->inputTemplate->cloneRegion('InputNoLabel');
        $labeled_group = $this->inputTemplate->cloneRegion('LabeledGroup');
        $no_label_group = $this->inputTemplate->cloneRegion('NoLabelGroup');

        $this->template->del('Content');

        foreach ($this->elements as $el) {
            // Buttons go under Button section
            if ($el instanceof \atk4\ui\Button) {
                $this->template->appendHTML('Buttons', $el->getHTML());

                continue;
            }

            if ($el instanceof self) {
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

            $template = $el->renderLabel ? $field_input : $field_no_label;
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
            $template->trySet('label_for', $el->id . '_input');
            $template->set('field_class', $el->getFieldClass());

            if ($el->field->required) {
                $template->append('field_class', 'required ');
            }

            if (isset($el->width)) {
                $template->append('field_class', $el->width . ' wide ');
            }

            if ($el->hint && $template->hasTag('Hint')) {
                $hint = $this->factory($this->defaultHint);
                $hint->id = $el->id . '_hint';
                if (is_object($el->hint) || is_array($el->hint)) {
                    $hint->add($el->hint);
                } else {
                    $hint->set($el->hint);
                }
                $template->setHTML('Hint', $hint->getHTML());
            } elseif ($template->hasTag('Hint')) {
                $template->del('Hint');
            }

            if ($this->template->hasTag($el->short_name)) {
                $this->template->trySetHTML($el->short_name, $template->render());
            } else {
                $this->template->appendHTML('Content', $template->render());
            }
        }

        // Now collect JS from everywhere
        foreach ($this->elements as $el) {
            if ($el->_js_actions) {
                $this->_js_actions = array_merge_recursive($this->_js_actions, $el->_js_actions);
            }
        }
    }
}
