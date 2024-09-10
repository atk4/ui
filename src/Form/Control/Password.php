<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Control;

use Atk4\Ui\Js\JsExpression;

class Password extends Line
{
    public string $inputType = 'password';

    /** Enable password reveal button */
    public bool $revealEye = false;

    #[\Override]
    protected function init(): void
    {
        parent::init();

        if ($this->revealEye) {
            $this->icon = 'grey eye link slash';
            if ($this->disabled) {
                $this->icon .= ' disabled';
            }
        }
    }

    #[\Override]
    protected function recursiveRender(): void
    {
        if ($this->revealEye && !$this->disabled) {
            $this->icon->on(
                'click',
                new JsExpression(
                    <<<'EOF'
                        let inputElem = document.getElementById([]);
                        let iconElem = document.getElementById([]);

                        if (inputElem.getAttribute('type') === 'password') {
                            inputElem.setAttribute('type', 'text');
                            iconElem.classList.remove('slash');
                            iconElem.classList.remove('grey');
                        } else {
                            inputElem.setAttribute('type', 'password');
                            iconElem.classList.add('slash');
                            iconElem.classList.add('grey');
                        }
                        EOF,
                    [$this->name . '_input', $this->icon->name]
                )
            );
        }

        parent::recursiveRender();
    }
}
