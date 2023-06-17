<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Control;

use Atk4\Ui\Exception;
use Atk4\Ui\Form;
use Atk4\Ui\Js\Jquery;
use Atk4\Ui\Js\JsExpressionable;

class Checkbox extends Form\Control
{
    public $ui = 'checkbox';

    public $defaultTemplate = 'form/control/checkbox.html';

    /**
     * Label appears to the right of the checkbox. If label is not set specifically
     * then the $caption property will be displayed as a label instead.
     *
     * @var string
     */
    public $label;

    public function __construct($label = [])
    {
        parent::__construct($label);

        $this->label = $this->content;
        $this->content = null;
    }

    protected function init(): void
    {
        parent::init();

        // checkboxes are annoying because they don't send value when they are not ticked
        if ($this->form) {
            $this->form->onHook(Form::HOOK_LOAD_POST, function (Form $form, array &$postRawData) {
                if (!isset($postRawData[$this->shortName])) {
                    $postRawData[$this->shortName] = '0';
                }
            });
        }
    }

    protected function renderView(): void
    {
        if ($this->label) {
            $this->template->set('Content', $this->label);
        }

        if ($this->entityField && !is_bool($this->entityField->get() ?? false)) {
            throw (new Exception('Checkbox form control requires field with boolean type'))
                ->addMoreInfo('type', $this->entityField->getField()->type)
                ->addMoreInfo('value', $this->entityField->get());
        }

        if ($this->entityField ? $this->entityField->get() : $this->content) {
            $this->template->dangerouslySetHtml('checked', 'checked="checked"');
        }

        $this->content = null;

        if ($this->disabled) {
            $this->addClass('disabled');
            $this->template->dangerouslySetHtml('disabled', 'disabled="disabled"');
        } elseif ($this->readOnly) {
            $this->addClass('read-only');
            $this->template->dangerouslySetHtml('disabled', 'readonly="readonly"');
        }

        $this->js(true)->checkbox();

        parent::renderView();
    }

    /**
     * Will return jQuery expression to get checkbox checked state.
     *
     * @param bool|string      $when
     * @param JsExpressionable $action
     *
     * @return Jquery
     */
    public function jsChecked($when = false, $action = null): JsExpressionable
    {
        return $this->jsInput($when, $action)->get(0)->checked;
    }
}
