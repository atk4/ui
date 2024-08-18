<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Control;

use Atk4\Ui\Button;
use Atk4\Ui\Js\JsExpression;

class Password extends Line
{
    public string $inputType = 'password';

    /** By setting this variable to true you get an Eye-button on the right side which can toggle the visibility of the entered password */
    public bool $eye;

    #[\Override]
    protected function init(): void
    {
        parent::init();

        if ($this->eye) {
            $button = new Button([
                'class.grey' => true,
                'iconRight' => 'eye slash',
            ]);

            $this->action = $button;

            $button->on(
                'click',
                new JsExpression(<<<EOF
		    var input_element = document.querySelector(`[id$='form_layout_{$this->shortName}_input']`);
		    var icon_element = document.querySelector(`[id$='{$this->shortName}_button_icon']`);

		    	if(input_element.getAttribute('type') == 'password') {
		      	    input_element.setAttribute('type', 'text');
			    icon_element.classList.remove(['slash']);
		   	} else {
		      	    input_element.setAttribute('type', 'password');
		      	    icon_element.classList.add(['slash']);
		    	}
			EOF
                )
            );
        }
    }
}
