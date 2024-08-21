<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Control;

use Atk4\Ui\Button;
use Atk4\Ui\Js\JsExpression;
use Atk4\Ui\Js\JsBlock;
use Atk4\Ui\Js\JsReload;
use Atk4\Ui\View;

class Password extends Line
{
    public string $inputType = 'password';

    /** Enable password reveal */
    public bool $revealEye = false;

    /** Storage for the added button */
    private $revealEyeButton;
    
    #[\Override]
    protected function init(): void
    {
        parent::init();

        if ($this->revealEye) {
            $this->revealEyeButton = Button::addTo($this, [
                    'class.tertiary event' => true,
                    'iconRight' => 'eye slash event',
                ],
                [
		    'AfterInput',
                ]
	    );
        }
    }

    #[\Override]
    protected function recursiveRender(): void
    {
        parent::recursiveRender();

        if ($this->revealEye && !$this->disabled) {
	    $this->js('click', 
                new JsExpression(
		    <<<'EOF'
                        let inputElem = document.getElementById([]);
                        let iconElem = document.getElementById([]);
                        console.log(inputElem);
                        console.log(iconElem);

                        if (inputElem.getAttribute('type') === 'password') {
                            inputElem.setAttribute('type', 'text');
                            iconElem.classList.remove(['slash']);
                        } else {
                            inputElem.setAttribute('type', 'password');
                            iconElem.classList.add(['slash']);
                        }
                        EOF,
                    [
                        $this->getHtmlId() . '_input',
		        $this->revealEyeButton->elements['icon']->getHtmlId(),
                    ]
                ),
            );
        }
    }
}
