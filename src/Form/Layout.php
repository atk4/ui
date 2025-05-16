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

    /** @var array<mixed> Seed for creating input hint View used in this layout. */
    public $defaultHintSeed = [Label::class, 'class' => ['pointing']];

    #[\Override]
    protected function _addControl(Control $control, Field $field): Control
    {
        return $this->add($control, ['desired_name' => $field->shortName]);
    }

    #[\Override]
    protected function init(): void
    {
        parent::init();

        if (!$this->inputTemplate) {
            $this->inputTemplate = $this->getApp()->loadTemplate($this->defaultInputTemplate);
        }
    }

    #[\Override]
    public function addButton($seed)
    {
        return $this->add(Factory::mergeSeeds([Button::class], $seed), 'Buttons');
    }

    /**
     * @param string|array<0|string, mixed> $label
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
     * @param string|array<0|string, mixed> $label
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

    #[\Override]
    protected function recursiveRender(): void
    {
        $labeledControl = $this->inputTemplate->cloneRegion('LabeledControl');
        $noLabelControl = $this->inputTemplate->cloneRegion('NoLabelControl');
        $labeledGroup = $this->inputTemplate->cloneRegion('LabeledGroup');
        $noLabelGroup = $this->inputTemplate->cloneRegion('NoLabelGroup');

        $this->template->del('Content');

        foreach ($this->elements as $element) {
            if ($element instanceof Button) {
                $this->renderElementAndAppendToTemplateRegion($element, 'Buttons');
            } elseif ($element instanceof self) {
                $this->recursiveRenderLayout($element, $labeledGroup, $noLabelGroup);
            } elseif ($element instanceof Control && $element->layoutWrap) {
                $this->recursiveRenderControl($element, $labeledControl, $noLabelControl);
            } else {
                $this->renderElementAndAppendToTemplateRegion($element, 'Content'); // @phpstan-ignore argument.type
            }
        }

        // collect JS from everywhere
        foreach ($this->elements as $view) {
            foreach ($view->_jsActions as $when => $actions) { // @phpstan-ignore property.notFound
                foreach ($actions as $action) {
                    $this->_jsActions[$when][] = $action;
                }
            }
        }
    }

    private function renderElementAndAppendToTemplateRegion(View $element, string $defaultRegion): void
    {
        $this->template->dangerouslyAppendHtml(
            $element->region && $this->template->hasTag($element->region)
                ? $element->region
                : $defaultRegion,
            $element->getHtml()
        );
    }

    protected function recursiveRenderLayout(self $layout, HtmlTemplate $labeledGroup, HtmlTemplate $noLabelGroup): void
    {
        if ($layout->label && !$layout->inline) {
            $template = $labeledGroup;
            $template->set('label', $layout->label);
        } else {
            $template = $noLabelGroup;
        }

        if ($layout->width) {
            $template->set('width', $layout->width);
        }

        if ($layout->inline) {
            $template->set('class', 'inline');
        }
        $template->dangerouslySetHtml('Content', $layout->getHtml());
        $this->template->dangerouslyAppendHtml(
            $layout->region && $this->template->hasTag($layout->region)
                ? $layout->region
                : 'Content',
            $template->renderToHtml()
        );
    }

    protected function recursiveRenderControl(Control $control, HtmlTemplate $labeledControl, HtmlTemplate $noLabelControl): void
    {
        $template = $control->renderLabel ? $labeledControl : $noLabelControl;
        $label = $control->caption;
        if ($label === null) {
            $label = $control->entityField->getField()->getCaption();
            if (property_exists($control, 'model')) {
                $label = preg_replace('~ ID$~i', '', $label);
            }
        }

        // anything but form controls gets inserted directly
        if ($control instanceof Control\Checkbox) {
            $template = $noLabelControl;
            $control->template->set('Content', $label);
        }

        if ($this->label && $this->inline) {
            if ($control instanceof Control\Input) {
                $control->placeholder = $label;
            }
            $label = $this->label;
            $this->label = null;
        } elseif ($this->label || $this->inline) {
            $template = $noLabelControl;
            if ($control instanceof Control\Input) {
                $control->placeholder = $label;
            }
        }

        // controls get extra pampering
        $template->dangerouslySetHtml('Input', $control->getHtml());
        $template->trySet('label', $label);
        $template->trySet('labelFor', $control->name . '_input');
        $template->set('controlClass', $control->controlClass);

        if ($control->entityField->getField()->required) {
            $template->append('controlClass', 'required ');
        }

        if ($control->width) {
            $template->append('controlClass', $control->width . ' wide ');
        }

        if ($control->hint && $template->hasTag('Hint')) {
            $hint = Factory::factory($this->defaultHintSeed);
            $hint->name = $control->name . '_hint';
            if (is_object($control->hint) || is_array($control->hint)) {
                $hint->add($control->hint);
            } else {
                $hint->set($control->hint);
            }
            $hint->setApp($this->getApp());
            $template->dangerouslySetHtml('Hint', $hint->getHtml());
        } elseif ($template->hasTag('Hint')) {
            $template->del('Hint');
        }

        if ($this->template->hasTag($control->shortName)) {
            $this->template->dangerouslySetHtml($control->shortName, $template->renderToHtml());
        } else {
            $this->template->dangerouslyAppendHtml('Content', $template->renderToHtml());
        }
    }
}
