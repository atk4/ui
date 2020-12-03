<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Control;

use Atk4\Ui\Exception;
use Atk4\Ui\Form;

/**
 * Input element for a form control.
 */
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

    /**
     * Constructor.
     *
     * @param string|array $label
     * @param string|array $class
     */
    public function __construct($label = null, $class = null)
    {
        parent::__construct($label, $class);

        $this->label = $this->content;
        $this->content = null;
    }

    /**
     * Initialization.
     */
    protected function init(): void
    {
        parent::init();

        // checkboxes are annoying because they don't send value when they are
        // not ticked. We assume they are ticked and sent boolean "false" as a
        // workaround. Otherwise send boolean "true".
        if ($this->form) {
            $this->form->onHook(Form::HOOK_LOAD_POST, function ($form, &$post) {
                $post[$this->field->short_name] = isset($post[$this->field->short_name]);
            });
        }
    }

    /**
     * Set field value.
     *
     * @param bool  $value
     * @param mixed $junk
     */
    public function set($value = null, $junk = null)
    {
        if (!is_bool($value)) {
            throw (new Exception('Field\Checkbox::set() needs value to be a boolean'))
                ->addMoreInfo('value', $value);
        }

        parent::set($value);
    }

    /**
     * Render view.
     */
    protected function renderView(): void
    {
        if ($this->label) {
            $this->template->set('Content', $this->label);
        }

        if ($this->field ? $this->field->get() : $this->content) {
            $this->template->set('checked', 'checked');
        }

        // We don't want this displayed, because it can only affect "checked" status anyway
        $this->content = null;

        // take care of readonly status
        if ($this->readonly) {
            $this->addClass('read-only');
        }

        // take care of disabled status
        if ($this->disabled) {
            $this->addClass('disabled');
            $this->template->set('disabled', 'disabled="disabled"');
        }

        $this->js(true)->checkbox();

        $this->content = null; // no content again

        parent::renderView();
    }

    /**
     * Will return jQuery expression to get checkbox checked state.
     *
     * @return Jquery
     */
    public function jsChecked($when = null, $action = null)
    {
        return $this->jsInput($when, $action)->get(0)->checked;
    }
}
