<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Control;

use Atk4\Ui\Button;
use Atk4\Ui\Js\JsExpression;

class Password extends Line
{
    public string $inputType = 'password';

    /** Enable password reveal */
    public bool $revealEye = false;

    /** @var Button|array|null */
    private $revealEyeButton;

    #[\Override]
    protected function init(): void
    {
        parent::init();
    }

    #[\Override]
    protected function recursiveRender(): void
    {
        if ($this->revealEye) {
            $this->revealEyeButton = Button::addTo(
                $this,
                [
                    'class.tertiary' => true,
                    'iconRight' => 'eye slash',
                ],
                [
                    'AfterInput',
                ]
            );
        }

        if ($this->revealEye && !$this->disabled) {
            $this->revealEyeButton->on(
                'click',
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
                        $this->revealEyeButton->getHtmlId(),
                    ]
                ),
            );
        }

        parent::recursiveRender();
    }
}
