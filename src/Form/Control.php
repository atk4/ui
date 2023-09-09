<?php

declare(strict_types=1);

namespace Atk4\Ui\Form;

use Atk4\Data\Field;
use Atk4\Data\Model;
use Atk4\Data\Model\EntityFieldPair;
use Atk4\Ui\Exception;
use Atk4\Ui\Form;
use Atk4\Ui\Js\Jquery;
use Atk4\Ui\Js\JsExpression;
use Atk4\Ui\Js\JsExpressionable;
use Atk4\Ui\View;

/**
 * Provides generic functionality for a form control.
 *
 * @phpstan-type JsCallbackSetClosure \Closure(Jquery, mixed, mixed, mixed, mixed, mixed, mixed, mixed, mixed, mixed, mixed): (JsExpressionable|View|string|void)
 */
class Control extends View
{
    /** @var Form|null to which this field belongs */
    public $form;

    /** @var EntityFieldPair<Model, Field>|null */
    public $entityField;

    /** @var string */
    public $controlClass = '';

    /** @var bool Whether you need this field to be rendered wrap in a form layout or as his */
    public bool $layoutWrap = true;

    /** @var bool rendered or not input label in generic Form\Layout template. */
    public $renderLabel = true;

    /** @var string Specify width for Fomantic-UI grid. For "four wide" use 'four'. */
    public $width;

    /**
     * Caption is a text that must appear somewhere nearby the field. For a form with layout, this
     * will typically place caption above the input field, but for checkbox this may appear next to the
     * checkbox itself. If Form Layout does not have captions above the input field, then caption
     * will appear as a placeholder of the input fields and it may also appear as a tooltip.
     *
     * Caption is usually specified by a model.
     *
     * @var string|null
     */
    public $caption;

    /**
     * Placed as a pointing label below the field. This only works when Form\Control appears in a form. You can also
     * set this to object, such as \Atk4\Ui\Text otherwise HTML characters are escaped.
     *
     * @var string|View|array
     */
    public $hint;

    /** Disabled field is not editable and will not be submitted. */
    public bool $disabled = false;

    /** Read-only field is not editable, but will be submitted. */
    public bool $readOnly = false;

    protected function init(): void
    {
        parent::init();

        if ($this->form && $this->entityField) {
            if (isset($this->form->controls[$this->entityField->getFieldName()])) {
                throw (new Exception('Form field already exists'))
                    ->addMoreInfo('name', $this->entityField->getFieldName());
            }
            $this->form->controls[$this->entityField->getFieldName()] = $this;
        }
    }

    /**
     * Sets the value of this field. If field is a part of the form and is associated with
     * the model, then the model's value will also be affected.
     *
     * @param mixed $value
     *
     * @return $this
     */
    public function set($value = null)
    {
        if ($this->entityField) {
            $this->entityField->set($value);
        } else {
            $this->content = $value;
        }

        return $this;
    }

    /**
     * It only makes sense to have "name" property inside a field if
     * it was used inside a form.
     */
    protected function renderView(): void
    {
        if ($this->form) {
            $this->template->trySet('name', $this->shortName);
        }

        parent::renderView();
    }

    protected function renderTemplateToHtml(): string
    {
        $output = parent::renderTemplateToHtml();

        $form = $this->getClosestOwner(Form::class);

        return $form !== null ? $form->fixOwningFormAttrInRenderedHtml($output) : $output;
    }

    /**
     * Shorthand method for on('change') event.
     * Some input fields, like Calendar, could call this differently.
     *
     * If $expr is JsExpressionable, then it will execute it instantly.
     * If $expr is callback method, then it'll make additional request to webserver.
     *
     * Examples:
     * $control->onChange(new JsExpression('console.log(\'changed\')'));
     * $control->onChange(new JsExpression('$(this).parents(\'.form\').form(\'submit\')'));
     *
     * @param JsExpressionable|JsCallbackSetClosure|array{JsCallbackSetClosure} $expr
     * @param array|bool                                                        $defaults
     */
    public function onChange($expr, $defaults = []): void
    {
        if (is_bool($defaults)) {
            $defaults = $defaults ? [] : ['preventDefault' => false, 'stopPropagation' => false];
        }

        $this->on('change', '#' . $this->name . '_input', $expr, $defaults);
    }

    /**
     * Method similar to View::js() however will adjust selector
     * to target the "input" element.
     *
     * $field->jsInput(true)->val(123);
     *
     * @param bool|string      $when
     * @param JsExpressionable $action
     *
     * @return Jquery
     */
    public function jsInput($when = false, $action = null): JsExpressionable
    {
        return $this->js($when, $action, '#' . $this->name . '_input');
    }
}
