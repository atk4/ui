<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Control;

use Atk4\Ui\Icon;
use Atk4\Ui\Js\JsReload;

class Password extends Line
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
            $iconInit = 'grey eye link';

            $this->inputType = $this->stickyGet('inputType') ?? 'password';
            $this->renderView();
            if ($this->inputType === 'password') {
                $iconInit .= ' slash';
            }

            $this->revealEyeIcon = Icon::addTo(
                $this,
                [
                    $iconInit,
                ],
                [
                    'AfterInput',
                ]
            );
        }

        if ($this->revealEye && !$this->disabled) {
            $this->revealEyeIcon->on(
                'click',
                function () {
                    parent::recursiveRender();
                    if ($this->inputType === 'password') {
                        $this->revealEyeIcon->js(true)->removeClass('slash');
                        $this->inputType = 'text';
                    } else {
                        $this->revealEyeIcon->js()->addClass('slash');
                        $this->inputType = 'password';
                    }

                    return new JsReload($this, ['inputType' => $this->inputType]);
                }
            );
        }

        parent::recursiveRender();
    }
}
