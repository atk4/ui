<?php

declare(strict_types=1);

namespace atk4\ui\Form;

use atk4\ui\Exception;
use atk4\ui\Label;
use atk4\ui\Template;

/**
 * Provides generic layout for a form.
 */
class Layout extends AbstractLayout
{
    /** {@inheritdoc} */
    public $defaultTemplate = 'form/layout/generic.html';

    /** @var string Default input template file. */
    public $defaultInputTemplate = 'form/layout/generic-input.html';

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

    protected function _addControl($decorator, $field)
    {
        return $this->add($decorator, ['desired_name' => $field->short_name]);
    }

    protected function init(): void
    {
        parent::init();

        if (!$this->inputTemplate) {
            $this->inputTemplate = $this->app->loadTemplate($this->defaultInputTemplate);
        }
    }

    /**
     * Adds Button.
     *
     * @param \atk4\ui\Button|array|string $seed
     *
     * @return \atk4\ui\Button
     */
    public function addButton($seed)
    {
        return $this->add($this->mergeSeeds([\atk4\ui\Button::class], $seed), 'Buttons');
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
     * @return static
     */
    public function addSubLayout($seed = [self::class], $addDivider = true)
    {
        $v = $this->add($this->factory($seed, ['form' => $this->form]));
        if ($v instanceof \atk4\ui\Form\Layout\Section) {
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
    protected function recursiveRender(): void
    {
        $labeledControl = $this->inputTemplate->cloneRegion('LabeledControl');
        $noLabelControl = $this->inputTemplate->cloneRegion('NoLabelControl');
        $labeledGroup = $this->inputTemplate->cloneRegion('LabeledGroup');
        $noLabelGroup = $this->inputTemplate->cloneRegion('NoLabelGroup');

        $this->template->del('Content');

        foreach ($this->elements as $element) {
            // Buttons go under Button section
            if ($element instanceof \atk4\ui\Button) {
                $this->template->appendHtml('Buttons', $element->getHtml());

                continue;
            }

            if ($element instanceof self) {
                if ($element->label && !$element->inline) {
                    $template = $labeledGroup;
                    $template->set('label', $element->label);
                } else {
                    $template = $noLabelGroup;
                }

                if ($element->width) {
                    $template->set('width', $element->width);
                }

                if ($element->inline) {
                    $template->set('class', 'inline');
                }
                $template->setHtml('Content', $element->getHtml());

                $this->template->appendHtml('Content', $template->render());

                continue;
            }

            // Anything but controls or explicitly defined controls get inserted directly
            if (!$element instanceof Control || !$element->layoutWrap) {
                $this->template->appendHtml('Content', $element->getHtml());

                continue;
            }

            $template = $element->renderLabel ? $labeledControl : $noLabelControl;
            $label = $element->caption ?: $element->field->getCaption();

            // Anything but form controls gets inserted directly
            if ($element instanceof \atk4\ui\Form\Control\Checkbox) {
                $template = $noLabelControl;
                $element->template->set('Content', $label);
            }

            if ($this->label && $this->inline) {
                $element->placeholder = $label;
                $label = $this->label;
                $this->label = null;
            } elseif ($this->label || $this->inline) {
                $template = $noLabelControl;
                $element->placeholder = $label;
            }

            // Controls get extra pampering
            $template->setHtml('Input', $element->getHtml());
            $template->trySet('label', $label);
            $template->trySet('label_for', $element->id . '_input');
            $template->set('control_class', $element->getControlClass());

            // BC-break exception - will be removed dec-2020
            if ($template->hasTag('field_class')) {
                throw new Exception('field_class region has be deprecated. Use control_class instead');
            }

            if ($element->field->required) {
                $template->append('control_class', 'required ');
            }

            if (isset($element->width)) {
                $template->append('control_class', $element->width . ' wide ');
            }

            if ($element->hint && $template->hasTag('Hint')) {
                $hint = $this->factory($this->defaultHint);
                $hint->id = $element->id . '_hint';
                if (is_object($element->hint) || is_array($element->hint)) {
                    $hint->add($element->hint);
                } else {
                    $hint->set($element->hint);
                }
                $template->setHtml('Hint', $hint->getHtml());
            } elseif ($template->hasTag('Hint')) {
                $template->del('Hint');
            }

            if ($this->template->hasTag($element->short_name)) {
                $this->template->trySetHtml($element->short_name, $template->render());
            } else {
                $this->template->appendHtml('Content', $template->render());
            }
        }

        // Now collect JS from everywhere
        foreach ($this->elements as $element) {
            if ($element->_js_actions) {
                $this->_js_actions = array_merge_recursive($this->_js_actions, $element->_js_actions);
            }
        }
    }
}
