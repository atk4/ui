<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Control;

use Atk4\Data\Field;
use Atk4\Data\Model\UserAction;
use Atk4\Ui\AbstractView;
use Atk4\Ui\Button;
use Atk4\Ui\Exception;
use Atk4\Ui\Form;
use Atk4\Ui\Icon;
use Atk4\Ui\Js\JsCallbackLoadableValue;
use Atk4\Ui\Label;
use Atk4\Ui\UserAction\ExecutorFactory;
use Atk4\Ui\UserAction\ExecutorInterface;
use Atk4\Ui\UserAction\JsCallbackExecutor;

class Input extends Form\Control
{
    public $ui = 'input';
    public $defaultTemplate = 'form/control/input.html';

    public string $inputType;

    /** @var string */
    public $placeholder = '';

    /** @var Icon|string|null */
    public $icon;

    /** @var Icon|string|null */
    public $iconLeft;

    /**
     * @var bool|'left'|'right' Specify left / right. If you use "true" will default to the right side.
     */
    public $loading;

    /**
     * Some fields also support $label. For Input the label can be placed to the left or to the right of
     * the field and you can fit currency symbol "$" inside a label for example.
     * For Input field label will appear on the left.
     *
     * @var string|Label
     */
    public $label;

    /** @var string|Label Set label that will appear to the right of the input field. */
    public $labelRight;

    /** @var Button|array<mixed>|UserAction|null */
    public $action;

    /** @var Button|array<mixed>|UserAction|null */
    public $actionLeft;

    /**
     * Additional attributes directly for the <input> tag can be added:
     * ['attribute_name' => 'attribute_value'], e.g.
     * ['autocomplete' => 'new-password'].
     *
     * Use setInputAttr() to fill this array
     *
     * @var array<string, string>
     */
    public array $inputAttr = [];

    /**
     * Set attribute which is added directly to the <input> tag, not the surrounding <div>.
     *
     * @param string|int|array<string, string|int>  $name
     * @param ($name is array ? never : string|int) $value
     *
     * @return $this
     */
    public function setInputAttr($name, $value = null)
    {
        if (is_array($name)) {
            foreach ($name as $k => $v) {
                $this->setInputAttr($k, $v);
            }
        } else {
            $this->inputAttr[$name] = $value;
        }

        return $this;
    }

    #[\Override]
    public function getInputValue(): ?string
    {
        if ($this->entityField !== null && $this->inputType === 'hidden') {
            return $this->getApp()->uiPersistence->typecastAttributeSaveField($this->entityField->getField(), $this->entityField->get());
        }

        return parent::getInputValue();
    }

    #[\Override]
    public function setInputValue(string $value): void
    {
        if ($this->entityField !== null && $this->inputType === 'hidden') {
            $this->set($this->getApp()->uiPersistence->typecastAttributeLoadField($this->entityField->getField(), $value));

            return;
        }

        parent::setInputValue($value);
    }

    public function getInputTag(): string
    {
        return $this->getApp()->getTag('input/', array_merge([
            'name' => $this->shortName,
            'type' => $this->inputType !== 'text' ? $this->inputType : false,
            'placeholder' => $this->inputType !== 'hidden' && $this->placeholder ? $this->placeholder : false,
            'id' => $this->name . '_input',
            'value' => $this->getInputValue(),
            'disabled' => $this->disabled && $this->inputType !== 'hidden',
            'readonly' => $this->readOnly && $this->inputType !== 'hidden' && !$this->disabled,
        ], $this->inputAttr));
    }

    /**
     * Used only from renderView().
     *
     * @param string|Label $label Label class or object
     * @param string       $spot  Template spot
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
     * @param string|array<mixed>|Button|UserAction|(AbstractView&ExecutorInterface) $button Button class or object
     * @param string                                                                 $spot   Template spot
     *
     * @return Button
     */
    protected function prepareRenderButton($button, $spot)
    {
        if (!is_object($button)) {
            $button = new Button($button);
        }
        if ($button instanceof UserAction || $button instanceof JsCallbackExecutor) {
            $executor = $button instanceof UserAction
                ? $this->getExecutorFactory()->createExecutor($button, $this, ExecutorFactory::JS_EXECUTOR)
                : $button;
            $button = $this->add($this->getExecutorFactory()->createTrigger($executor->getAction()), $spot);

            if (count($executor->getAction()->args) === 0) {
                $button->on('click', $executor);
            } elseif (count($executor->getAction()->args) === 1) {
                $actionArgName = array_key_first($executor->getAction()->args);
                $actionArgType = $executor->getAction()->args[$actionArgName]['type'];

                $button->on('click', $executor, ['args' => [
                    $actionArgName => new JsCallbackLoadableValue($this->jsInput()->val(), function ($v) use ($actionArgType) {
                        return $this->getApp()->uiPersistence->typecastLoadField(
                            new Field(['type' => $actionArgType]),
                            $v
                        );
                    }),
                ]]);
            } else {
                throw (new Exception('Input form control supports user action with zero or one argument only'))
                    ->addMoreInfo('arguments', array_keys($executor->getAction()->args));
            }
        }
        if (!$button->isInitialized()) { // TODO if should be replaced with new method like View::addOrAssertRegion() which will add the element and otherwise assert the owner and region
            $this->add($button, $spot);
        }

        return $button;
    }

    #[\Override]
    protected function renderView(): void
    {
        // TODO: I don't think we need the loading state at all
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
            if (!$this->actionLeft) {
                $this->addClass('action');
            }
        }

        if ($this->actionLeft) {
            $this->actionLeft = $this->prepareRenderButton($this->actionLeft, 'BeforeInput');
            $this->addClass('left action');
        }

        // set template
        $this->template->dangerouslySetHtml('Input', $this->getInputTag());
        $this->content = null;

        parent::renderView();
    }

    /**
     * Adds new action button.
     *
     * @param array<mixed> $defaults
     *
     * @return Button
     */
    public function addAction(array $defaults = [])
    {
        $this->action = Button::addTo($this, $defaults, ['AfterInput']);

        return $this->action;
    }
}
