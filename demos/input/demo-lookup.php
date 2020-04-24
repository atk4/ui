<?php


/**
 * Setup file - do not test.
 * Lookup that can not saved data.
 */
class DemoLookup extends \atk4\ui\FormField\Lookup
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

        $defaultSeed = ['Button', 'disabled' => ($this->disabled || $this->readonly)];

        $this->action = $this->factory(array_merge($defaultSeed, (array) $buttonSeed), null, 'atk4\ui');

        if ($this->form) {
            $vp = \atk4\ui\VirtualPage::addTo($this->form);
        } else {
            $vp = \atk4\ui\VirtualPage::addTo($this->owner);
        }

        $vp->set(function ($page) {
            $form = \atk4\ui\Form::addTo($page);

            $model = clone $this->model;

            $form->setModel($model->onlyFields($this->plus['fields'] ?? []));

            $form->onSubmit(function ($form) {
                //Prevent from saving
                // $form->model->save();

                $ret = [
                    new \atk4\ui\jsToast('Form submit!. Demo can not saved data.'),
                    (new \atk4\ui\jQuery('.atk-modal'))->modal('hide'),
                ];

                if ($row = $this->renderRow($form->model)) {
                    $chain = new \atk4\ui\jQuery('#' . $this->name . '-ac');
                    $chain->dropdown('set value', $row['value'])->dropdown('set text', $row['title']);

                    $ret[] = $chain;
                }

                return $ret;
            });
        });

        $caption = $this->plus['caption'] ?? 'Add New ' . $this->model->getModelCaption();

        $this->action->js('click', new \atk4\ui\jsModal($caption, $vp));
    }
}

$demoLookup = new DemoLookup();
