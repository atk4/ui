<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Core\Factory;
use Atk4\Ui\Button;
use Atk4\Ui\Form;
use Atk4\Ui\JsToast;
use Atk4\Ui\VirtualPage;

/**
 * Setup file - do not test.
 * Lookup that cannot saved data.
 */
class DemoLookup extends Form\Control\Lookup
{
    /**
     * Add button for new record.
     */
    protected function initQuickNewRecord()
    {
        if (!$this->plus) {
            return;
        }

        $this->plus = is_bool($this->plus) ? 'Add New' : $this->plus;

        $this->plus = is_string($this->plus) ? ['button' => $this->plus] : $this->plus;

        $buttonSeed = $this->plus['button'] ?? [];

        $buttonSeed = is_string($buttonSeed) ? ['content' => $buttonSeed] : $buttonSeed;

        $defaultSeed = [Button::class, 'class.disabled' => ($this->disabled || $this->readOnly)];

        $this->action = Factory::factory(array_merge($defaultSeed, (array) $buttonSeed));

        $vp = VirtualPage::addTo($this->form ?? $this->getOwner());
        $vp->set(function ($page) {
            $form = Form::addTo($page);

            $entity = $this->model->createEntity();
            $form->setModel($entity, $this->plus['fields'] ?? null);

            $form->onSubmit(function (Form $form) {
                $form->model->save();

                $ret = [
                    new JsToast('Form submit!. Data are not save in demo mode.'),
                    (new \Atk4\Ui\Jquery('.atk-modal'))->modal('hide'),
                ];

                $row = $this->renderRow($form->model);
                $chain = new \Atk4\Ui\Jquery('#' . $this->name . '-ac');
                $chain->dropdown('set value', $row['value'])->dropdown('set text', $row['title']);
                $ret[] = $chain;

                return $ret;
            });
        });

        $caption = $this->plus['caption'] ?? 'Add New ' . $this->model->getModelCaption();

        $this->action->js('click', new \Atk4\Ui\JsModal($caption, $vp));
    }
}
