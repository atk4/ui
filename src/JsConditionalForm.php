<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Core\DiContainerTrait;

/**
 * Implements conditions for displaying fields on form.
 */
class JsConditionalForm implements JsExpressionable
{
    use DiContainerTrait;

    /** @var Form The form where rules should apply. */
    public $form;

    /** @var array|null The field rules for that form. */
    public $fieldRules = [];

    /** @var string The html class name parent for input. */
    public $selector;

    public function __construct(Form $form, array $rules = null, string $selector = '.field')
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
    public function setRules($rules): void
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
}
