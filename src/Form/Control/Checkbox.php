<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Control;

use Atk4\Ui\Exception;
use Atk4\Ui\Form;
use Atk4\Ui\Jquery;
use Atk4\Ui\JsExpressionable;

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
        if (func_num_args() > 1) { // prevent bad usage
            throw new \Error('Too many method arguments');
        }

        parent::__construct($label);

        $this->label = $this->content;
        $this->content = null;
    }

    protected function init(): void
    {
        // TODO exception should be generalized for type acceptable for any form control
        if ($this->entityField && $this->entityField->getField()->type !== 'boolean') {
            throw (new Exception('Checkbox form control requires field with boolean type'))
                ->addMoreInfo('type', $this->entityField->getField()->type);
        }

        parent::init();

        // checkboxes are annoying because they don't send value when they are
        // not ticked. We assume they are ticked and sent boolean "false" as a
        // workaround. Otherwise send boolean "true".
        if ($this->form) {
            $this->form->onHook(Form::HOOK_LOAD_POST, function (Form $form, array &$postRawData) {
                $postRawData[$this->entityField->getFieldName()] = isset($postRawData[$this->entityField->getFieldName()]);
            });
        }
    }

    protected function renderView(): void
    {
        if ($this->label) {
            $this->template->set('Content', $this->label);
        }

        if ($this->entityField ? $this->entityField->get() : $this->content) {
            $this->template->dangerouslySetHtml('checked', 'checked="checked"');
        }

        // We don't want this displayed, because it can only affect "checked" status anyway
        $this->content = null;

        if ($this->readOnly) {
            $this->addClass('read-only');
        }

        if ($this->disabled) {
            $this->addClass('disabled');
            $this->template->dangerouslySetHtml('disabled', 'disabled="disabled"');
        }

        $this->js(true)->checkbox();

        $this->content = null; // no content again

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
    public function jsChecked($when = false, $action = null)
    {
        return $this->jsInput($when, $action)->get(0)->checked;
    }
}
