<?php

declare(strict_types=1);

namespace Atk4\Ui\Form;

use Atk4\Core\Factory;
use Atk4\Data\Field;
use Atk4\Ui\Button;
use Atk4\Ui\Header;
use Atk4\Ui\HtmlTemplate;
use Atk4\Ui\Label;
use Atk4\Ui\View;

/**
 * Provides generic layout for a form.
 */
class Layout extends AbstractLayout
{
    public $defaultTemplate = 'form/layout/generic.html';

    /** @var string Default input template file. */
    public $defaultInputTemplate = 'form/layout/generic-input.html';

    /** @var string|null If specified will appear on top of the group. Can be string or Label object. */
    public $label;

    /**
     * Specify width of a group in numerical word e.g. 'width' => 'two' as per
     * Fomantic-UI grid system.
     *
     * @var string
     */
    public $width;

    /** @var bool Set true if you want fields to appear in-line. */
    public $inline = false;

    /** @var HtmlTemplate|null Template holding input HTML. */
    public $inputTemplate;

    /** @var array Seed for creating input hint View used in this layout. */
    public $defaultHint = [Label::class, 'class' => ['pointing']];

    protected function _addControl(Control $control, Field $field): Control
    {
        return $this->add($control, ['desired_name' => $field->shortName]);
    }

    protected function init(): void
    {
        parent::init();

        if (!$this->inputTemplate) {
            $this->inputTemplate = $this->getApp()->loadTemplate($this->defaultInputTemplate);
        }
    }

    /**
     * @param Button|array $seed
     *
     * @return Button
     */
    public function addButton($seed)
    {
        return $this->add(Factory::mergeSeeds([Button::class], $seed), 'Buttons');
    }

    /**
     * @param string|array $label
     *
     * @return $this
     */
    public function addHeader($label)
    {
        Header::addTo($this, [$label, 'class.dividing' => true, 'element' => 'h4']);

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
     * @return self
     */
    public function addSubLayout($seed = [self::class], $addDivider = true)
    {
        $v = $this->add(Factory::factory($seed, ['form' => $this->form]));
        if ($v instanceof Layout\Section) {
            $v = $v->addSection();
        }

        if ($addDivider) {
            View::addTo($this, ['ui' => 'hidden divider']);
        }

        return $v;
    }

    protected function recursiveRender(): void
    {
        $labeledControl = $this->inputTemplate->cloneRegion('LabeledControl');
        $noLabelControl = $this->inputTemplate->cloneRegion('NoLabelControl');
        $labeledGroup = $this->inputTemplate->cloneRegion('LabeledGroup');
        $noLabelGroup = $this->inputTemplate->cloneRegion('NoLabelGroup');

        $this->template->del('Content');

        foreach ($this->elements as $element) {
            // buttons go under Button section
            if ($element instanceof Button) {
                $this->template->dangerouslyAppendHtml('Buttons', $element->getHtml());

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
                $template->dangerouslySetHtml('Content', $element->getHtml());

                $this->template->dangerouslyAppendHtml('Content', $template->renderToHtml());

                continue;
            }

            // anything but controls or explicitly defined controls get inserted directly
            if (!$element instanceof Control || !$element->layoutWrap) {
                $this->template->dangerouslyAppendHtml('Content', $element->getHtml()); // @phpstan-ignore-line

                continue;
            }

            $template = $element->renderLabel ? $labeledControl : $noLabelControl;
            $label = $element->caption ?? $element->entityField->getField()->getCaption();

            // anything but form controls gets inserted directly
            if ($element instanceof Control\Checkbox) {
                $template = $noLabelControl;
                $element->template->set('Content', $label);
            }

            if ($this->label && $this->inline) {
                if ($element instanceof Control\Input) {
                    $element->placeholder = $label;
                }
                $label = $this->label;
                $this->label = null;
            } elseif ($this->label || $this->inline) {
                $template = $noLabelControl;
                if ($element instanceof Control\Input) {
                    $element->placeholder = $label;
                }
            }

            // controls get extra pampering
            $template->dangerouslySetHtml('Input', $element->getHtml());
            $template->trySet('label', $label);
            $template->trySet('labelFor', $element->name . '_input');
            $template->set('controlClass', $element->controlClass);

            if ($element->entityField->getField()->required) {
                $template->append('controlClass', 'required ');
            }

            if ($element->width) {
                $template->append('controlClass', $element->width . ' wide ');
            }

            if ($element->hint && $template->hasTag('Hint')) {
                $hint = Factory::factory($this->defaultHint);
                $hint->name = $element->name . '_hint';
                if (is_object($element->hint) || is_array($element->hint)) {
                    $hint->add($element->hint);
                } else {
                    $hint->set($element->hint);
                }
                $hint->setApp($this->getApp());
                $template->dangerouslySetHtml('Hint', $hint->getHtml());
            } elseif ($template->hasTag('Hint')) {
                $template->del('Hint');
            }

            if ($this->template->hasTag($element->shortName)) {
                $this->template->dangerouslySetHtml($element->shortName, $template->renderToHtml());
            } else {
                $this->template->dangerouslyAppendHtml('Content', $template->renderToHtml());
            }
        }

        // collect JS from everywhere
        foreach ($this->elements as $view) {
            foreach ($view->_jsActions as $when => $actions) { // @phpstan-ignore-line
                foreach ($actions as $action) {
                    $this->_jsActions[$when][] = $action;
                }
            }
        }
    }
}
