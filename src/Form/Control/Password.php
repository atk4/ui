<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Control;

use Atk4\Ui\Button;
use Atk4\Ui\Js\JsExpression;

class Password extends Line
{
    public string $inputType = 'password';

    /** By setting this variable to true you get an Eye-button on the right side which can toggle the visibility of the entered password */
    public bool $revealEye = false;

    #[\Override]
    protected function init(): void
    {
        parent::init();

        if ($this->revealEye) {
            $button = new Button([
                'class.grey' => true,
                'iconRight' => 'eye slash',
            ]);

            $this->action = $button;

            $button->on(
                'click',
                new JsExpression(
                    <<<"EOF"
                        let inputElem = document.getElementById([] + '_input');
                        let iconElem = document.querySelector('[id$="' + [] + '_button_icon"]');

                        if (inputElem.getAttribute('type') === 'password') {
                            inputElem.setAttribute('type', 'text');
                            iconElem.classList.remove(['slash']);
                        } else {
                            inputElem.setAttribute('type', 'password');
                            iconElem.classList.add(['slash']);
                        }
                        EOF, [$this->name, $this->shortName]
                )
            );
        }
    }
}
