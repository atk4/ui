<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Core\Factory;
use Atk4\Ui\Button;
use Atk4\Ui\Form;
use Atk4\Ui\Js\Jquery;
use Atk4\Ui\Js\JsModal;
use Atk4\Ui\Js\JsToast;
use Atk4\Ui\VirtualPage;

class DemoLookup extends Form\Control\Lookup
{
    protected function initQuickNewRecord(): void
    {
        if (!$this->plus) {
            return;
        }

        if ($this->plus === true) {
            $this->plus = 'Add New';
        }

        if (is_string($this->plus)) {
            $this->plus = ['button' => $this->plus];
        }

        $buttonSeed = $this->plus['button'] ?? [];
        if (is_string($buttonSeed)) {
            $buttonSeed = ['content' => $buttonSeed];
        }

        $defaultSeed = [Button::class, 'class.disabled' => $this->disabled || $this->readOnly];
        $this->action = Factory::factory(array_merge($defaultSeed, $buttonSeed));

        $vp = VirtualPage::addTo($this->form ?? $this->getOwner());
        $vp->set(function (VirtualPage $vp) {
            $form = Form::addTo($vp);

            $entity = $this->model->createEntity();
            $form->setModel($entity, $this->plus['fields'] ?? null);

            $form->onSubmit(function (Form $form) {
                $msg = $form->model->getUserAction('add')->execute();

                $ret = [
                    new JsToast($msg),
                    (new Jquery())->closest('.atk-modal')->modal('hide'),
                ];

                $row = $this->renderRow($form->model);
                $chain = new Jquery('#' . $this->name . '-ac');
                $chain->dropdown('set value', $row['value'])->dropdown('set text', $row['title']);
                $ret[] = $chain;

                return $ret;
            });
        });

        $caption = $this->plus['caption'] ?? 'Add New ' . $this->model->getModelCaption();
        $this->action->on('click', new JsModal($caption, $vp));
    }
}
