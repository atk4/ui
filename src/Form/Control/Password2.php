<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Control;

use Atk4\Ui\Icon;
use Atk4\Ui\Js\JsExpression;

class Password2 extends Line
{
    public string $inputType = 'password';

    /** Enable password reveal */
    public bool $revealEye = false;

    /** @var Icon|array|null */
    private $revealEyeIcon;

    #[\Override]
    protected function init(): void
    {
        parent::init();

        if ($this->revealEye) {
            if (!$this->icon) {
                $this->icon = 'true';
            }
        }
    }

    #[\Override]
    protected function recursiveRender(): void
    {
        if ($this->revealEye) {
            $this->revealEyeIcon = Icon::addTo(
                $this,
                [
                    'grey eye slash link',
                ],
                [
                    'AfterInput',
                ]
            );
        }

        if ($this->revealEye && !$this->disabled) {
            $this->revealEyeIcon->on(
                'click',
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
                        $this->revealEyeIcon->getHtmlId(),
                    ]
                )
            );
        }

        parent::recursiveRender();
    }
}
