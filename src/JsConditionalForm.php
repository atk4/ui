<?php

declare(strict_types=1);

namespace Atk4\Ui;

/**
 * Implements conditions for displaying fields on form.
 */
class JsConditionalForm implements JsExpressionable
{
    use \Atk4\Core\DiContainerTrait;

    // {{{ Properties

    /**
     * The form where rules should apply.
     *
     * @var Form
     */
    public $form;

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

    public function __construct(Form $form, $rules = null, $selector = '.field')
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

    public function jsRender(): string
    {
        $chain = (new Jquery($this->form))
            ->atkConditionalForm([
                'fieldRules' => $this->fieldRules,
                'selector' => $this->selector,
            ]);

        return $chain->jsRender();
    }

    // }}}
}
