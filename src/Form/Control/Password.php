<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Control;

use Atk4\Ui\Button;
use Atk4\Ui\Js\JsExpression;
use Atk4\Ui\View;

class Password extends Line
{
    public string $inputType = 'password';

    /** Enable password reveal */
    public bool $revealEye = false;

    #[\Override]
    protected function init(): void
    {
        parent::init();

        if ($this->revealEye) {
            $this->action = Button::addTo($this, [
                'class.grey' => true,
                'iconRight' => 'eye slash',
            ], ['AfterInput']);
        }
    }

    #[\Override]
    protected function recursiveRender(): void
    {
        parent::recursiveRender();

        if ($this->revealEye && !$this->disabled) {
            $this->on( // TODO $this->action->on() call is too late here and must throw
                'click',
                $this->action, // @phpstan-ignore argument.type
                new JsExpression(
                    <<<'EOF'
                        let inputElem = document.getElementById([]);
                        let iconElem = document.getElementById([]);

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
                        View::assertInstanceOf($this->action->elements['icon'])->getHtmlId(),
                    ]
                )
            );
        }
    }
}
