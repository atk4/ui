<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Control;

use Atk4\Data\Model\UserAction;
use Atk4\Ui\Button;
use Atk4\Ui\Form;
use Atk4\Ui\Icon;
use Atk4\Ui\Label;
use Atk4\Ui\UserAction\JsCallbackExecutor;

/**
 * Input element for a form control.
 */
class Input extends Form\Control
{
    public $ui = 'input';

    public $inputType = 'text';

    public $placeholder = '';

    public $defaultTemplate = 'form/control/input.html';

    public $icon;

    public $iconLeft;

    /**
     * Specify left / right. If you use "true" will default to the right side.
     */
    public $loading;

    /**
     * Some fields also support $label. For Input the label can be placed to the left or to the right of
     * the field and you can fit currency symbol "$" inside a label for example.
     * For Input field label will appear on the left.
     *
     * @var string|object
     */
    public $label;

    /**
     * Set label that will appear to the right of the input field.
     *
     * @var string|object
     */
    public $labelRight;

    public $action;

    public $actionLeft;

    /**
     * Specify width for semantic UI grid. For "four wide" use 'four'.
     */
    public $width;

    /**
     * here additional attributes directly for the <input> tag can be added:
     * ['attribute_name' => 'attribute_value'], e.g.
     * ['autocomplete' => 'new-password'].
     *
     * Use setInputAttr() to fill this array
     *
     * @var array
     */
    public $inputAttr = [];

    /**
     * Set attribute which is added directly to the <input> tag, not the surrounding <div>.
     *
     * @param string|array $attr  Attribute name or hash
     * @param string       $value Attribute value
     *
     * @return $this
     */
    public function setInputAttr($attr, $value = null)
    {
        if (is_array($attr)) {
            $this->inputAttr = array_merge($this->inputAttr, $attr);

            return $this;
        }

        $this->inputAttr[$attr] = $value;

        return $this;
    }

    /**
     * Returns presentable value to be inserted into input tag.
     *
     * @return mixed
     */
    public function getValue()
    {
        return isset($this->field)
                    ? $this->getApp()->ui_persistence->typecastSaveField($this->field, $this->field->get())
                    : ($this->content ?? '');
    }

    /**
     * Returns <input .../> tag.
     *
     * @return string
     */
    public function getInput()
    {
        return $this->getApp()->getTag('input', array_merge([
            'name' => $this->short_name,
            'type' => $this->inputType,
            'placeholder' => $this->placeholder,
            'id' => $this->id . '_input',
            'value' => $this->getValue(),
            'readonly' => $this->readonly ? 'readonly' : false,
            'disabled' => $this->disabled ? 'disabled' : false,
        ], $this->inputAttr));
    }

    /**
     * Used only from renderView().
     *
     * @param string|object $label Label class or object
     * @param string        $spot  Template spot
     *
     * @return Label
     */
    protected function prepareRenderLabel($label, $spot)
    {
        if (!is_object($label)) {
            $label = Label::addTo($this, [], [$spot])
                ->set($label);
        } else {
            $this->add($label, $spot);
        }

        if ($label->ui !== 'label') {
            $label->addClass('label');
        }

        return $label;
    }

    /**
     * Used only from renderView().
     *
     * @param string|object $button Button class or object
     * @param string        $spot   Template spot
     *
     * @return Button
     */
    protected function prepareRenderButton($button, $spot)
    {
        if (!is_object($button)) {
            $button = new Button($button);
        }
        if ($button instanceof UserAction || $button instanceof JsCallbackExecutor) {
            $executor = ($button instanceof UserAction)
                ? $this->getExecutorFactory()->create($button, $this, $this->getExecutorFactory()::JS_EXECUTOR)
                : $button;
            $button = $this->add($this->getExecutorFactory()->createTrigger($executor->getAction()), $spot);
            $this->addClass('action');
            if ($executor->getAction()->args) {
                $val_as_arg = array_keys($executor->getAction()->args)[0];

                $button->on('click', $executor, ['args' => [$val_as_arg => $this->jsInput()->val()]]);
            } else {
                $button->on('click', $executor);
            }
        }
        if (!$button->_initialized) {
            $this->add($button, $spot);
            $this->addClass('action');
        }

        return $button;
    }

    protected function renderView(): void
    {
        // TODO: I don't think we need the loading state at all.
        if ($this->loading) {
            if (!$this->icon) {
                $this->icon = 'search'; // does not matter, but since
            }

            $this->addClass('loading');

            if ($this->loading === 'left') {
                $this->addClass('left');
            }
        }

        // icons
        if ($this->icon && !is_object($this->icon)) {
            $this->icon = Icon::addTo($this, [$this->icon], ['AfterInput']);
            $this->addClass('icon');
        }

        if ($this->iconLeft && !is_object($this->iconLeft)) {
            $this->iconLeft = Icon::addTo($this, [$this->iconLeft], ['BeforeInput']);
            $this->addClass('left icon');
        }

        // labels
        if ($this->label) {
            $this->label = $this->prepareRenderLabel($this->label, 'BeforeInput');
        }

        if ($this->labelRight) {
            $this->labelRight = $this->prepareRenderLabel($this->labelRight, 'AfterInput');
            $this->addClass('right');
        }

        if ($this->label || $this->labelRight) {
            $this->addClass('labeled');
        }

        // width
        if ($this->width) {
            $this->addClass($this->width . ' wide');
        }

        // actions
        if ($this->action) {
            $this->action = $this->prepareRenderButton($this->action, 'AfterInput');
        }

        if ($this->actionLeft) {
            $this->actionLeft = $this->prepareRenderButton($this->actionLeft, 'BeforeInput');
            $this->addClass('left');
        }

        // set template
        $this->template->dangerouslySetHtml('Input', $this->getInput());
        $this->content = null;

        parent::renderView();
    }

    /**
     * Adds new action button.
     *
     * @param array $defaults
     *
     * @return Button
     */
    public function addAction($defaults = [])
    {
        if (!is_array($defaults)) {
            $defaults = [$defaults];
        }

        $this->action = Button::addTo($this, [$defaults], ['AfterInput']);
        $this->addClass('action');

        return $this->action;
    }
}
