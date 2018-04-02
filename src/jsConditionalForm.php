<?php

namespace atk4\ui;

class jsConditionalForm implements jsExpressionable
{
    use \atk4\core\DIContainerTrait;

    public $form = null;
    public $fieldRules = [];
    public $selector = '.field';

    public function __construct($form, $rules = null)
    {
        $this->form = $form;
        $this->fieldRules = $rules;
    }

    public function setRules($rules)
    {
        $this->fieldRules = $rules;
    }

    public function jsRender()
    {
       $chain = (new jQuery($this->form))
                ->atkConditionalForm(
                    ['fieldRules' => $this->fieldRules,
                     'selector' => $this->selector]
                );

       return $chain->jsRender();
    }
}