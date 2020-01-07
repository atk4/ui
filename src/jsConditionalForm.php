<?php

namespace atk4\ui;

/**
 * Implements conditions for displaying fields on form.
 */
class jsConditionalForm implements jsExpressionable
{
    use \atk4\core\DIContainerTrait;

    // {{{ Properties

    /**
     * The form where rules should apply.
     *
     * @var null
     */
    public $form = null;

    /**
     * The field rules for that form.
     *
     * @var array|null
     */
    public $fieldRules = [];

    /**
     * The html class name parent for input.
     *
     * @var string
     */
    public $selector;

    // }}}

    // {{{ Base Methods

    public function __construct($form, $rules = null, $selector = '.field')
    {
        $this->form = $form;
        $this->fieldRules = $rules;
        $this->selector = $selector;
    }

    /**
     * Set field rules for the form.
     *
     * @param array $rules
     */
    public function setRules($rules)
    {
        $this->fieldRules = $rules;
    }

    public function jsRender()
    {
        $chain = (new jQuery($this->form))
                ->atkConditionalForm(
                    ['fieldRules'    => $this->fieldRules,
                        'selector'   => $this->selector, ]
                );

        return $chain->jsRender();
    }

    // }}}
}
