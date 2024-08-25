<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Control;

use Atk4\Ui\Icon;
use Atk4\Ui\Js\JsBlock;
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

    protected function delArrValues(array $arr, array $remove)
    {
        return array_filter($arr, static fn($e) => !in_array($e, $remove));
    }
    
    #[\Override]
    protected function recursiveRender(): void
    {
        if ($this->revealEye) {
            if ($this->stickyGet('iconInit')) {
                $iconInit = $this->stickyGet('iconInit');
                $this->inputType = $this->stickyGet('inputType');
                $this->renderView();
            } else {
                $iconInit = 'grey eye slash link';
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
                        $this->inputType = 'text';
                    } else {
                        $this->inputType = 'password';
                    }
                    if (in_array('slash', $this->revealEyeIcon->class, true)) {
                        $reIcon = $this->revealEyeIcon->js(true)->removeClass('slash');
                        $this->revealEyeIcon->class = $this->delArrValues($this->revealEyeIcon->class, ['slash']);
                    } else {
                        $reIcon = $this->revealEyeIcon->js()->addClass('slash');
                        $this->revealEyeIcon->class[] = 'slash';
                    }
                    $iconInit = implode(' ', $this->revealEyeIcon->class);
                    
                    return new JsBlock([
                        $reIcon,
                        new JsReload($this, ['iconInit' => $iconInit, 'inputType' => $this->inputType]),
                    ]);
                }
            );
        }
        
        parent::recursiveRender();
    }
}
