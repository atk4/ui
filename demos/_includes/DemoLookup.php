<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Core\Factory;

/**
 * Setup file - do not test.
 * Lookup that can not saved data.
 */
class DemoLookup extends \Atk4\Ui\Form\Control\Lookup
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

        $defaultSeed = [\Atk4\Ui\Button::class, 'disabled' => ($this->disabled || $this->readonly)];

        $this->action = Factory::factory(array_merge($defaultSeed, (array) $buttonSeed));

        if ($this->form) {
            $vp = \Atk4\Ui\VirtualPage::addTo($this->form);
        } else {
            $vp = \Atk4\Ui\VirtualPage::addTo($this->getOwner());
        }

        $vp->set(function ($page) {
            $form = \Atk4\Ui\Form::addTo($page);

            $model = clone $this->model;

            $form->setModel($model->onlyFields($this->plus['fields'] ?? []));

            $form->onSubmit(function (\Atk4\Ui\Form $form) {
                // Prevent from saving
                // $form->model->save();

                $ret = [
                    new \Atk4\Ui\JsToast('Form submit!. Demo can not saved data.'),
                    (new \Atk4\Ui\Jquery('.atk-modal'))->modal('hide'),
                ];

                if ($row = $this->renderRow($form->model)) {
                    $chain = new \Atk4\Ui\Jquery('#' . $this->name . '-ac');
                    $chain->dropdown('set value', $row['value'])->dropdown('set text', $row['title']);

                    $ret[] = $chain;
                }

                return $ret;
            });
        });

        $caption = $this->plus['caption'] ?? 'Add New ' . $this->model->getModelCaption();

        $this->action->js('click', new \Atk4\Ui\JsModal($caption, $vp));
    }
}
