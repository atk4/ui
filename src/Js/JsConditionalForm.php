<?php

declare(strict_types=1);

namespace Atk4\Ui\Js;

use Atk4\Core\DiContainerTrait;
use Atk4\Ui\Form;

class JsConditionalForm implements JsExpressionable
{
    use DiContainerTrait;

    /** @var Form The form where rules should apply. */
    public $form;

    /** @var array The field rules for that form. */
    public array $fieldRules;

    /** @var string The HTML class name parent for input. */
    public $selector;

    public function __construct(Form $form, array $rules = [], string $selector = '.field')
    {
        $this->form = $form;
        $this->fieldRules = $rules;
        $this->selector = $selector;
    }

    /**
     * Set field rules for the form.
     */
    public function setRules(array $rules): void
    {
        $this->fieldRules = $rules;
    }

    #[\Override]
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
