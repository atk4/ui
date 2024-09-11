<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Control;

use Atk4\Ui\Js\JsExpression;

class Password extends Line
{
    public string $inputType = 'password';

    /** Enable reveal button */
    public bool $revealEye = true;

    #[\Override]
    protected function init(): void
    {
        parent::init();

        if ($this->revealEye) {
            $this->icon = 'eye link slash';
            if ($this->disabled) {
                $this->icon .= ' grey disabled';
            }
        }
    }

    #[\Override]
    protected function recursiveRender(): void
    {
        if ($this->revealEye && !$this->disabled) {
            $this->icon->on(
                'mousedown', // do not use 'click' to keep focus/selection
                new JsExpression(
                    <<<'EOF'
                        let inputElem = document.getElementById([]);
                        let iconElem = document.getElementById([]);

                        if (inputElem.getAttribute('type') === 'password') {
                            inputElem.setAttribute('type', 'text');
                            iconElem.classList.remove('slash');
                        } else {
                            inputElem.setAttribute('type', 'password');
                            iconElem.classList.add('slash');
                        }

                        if (document.activeElement !== inputElem) {
                            inputElem.setSelectionRange(-1, -1);
                            inputElem.focus();
                        }
                        EOF,
                    [$this->name . '_input', $this->icon->name]
                )
            );
        }

        parent::recursiveRender();
    }
}
