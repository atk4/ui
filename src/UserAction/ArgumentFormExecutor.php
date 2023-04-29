<?php

declare(strict_types=1);

namespace Atk4\Ui\UserAction;

use Atk4\Core\Factory;
use Atk4\Data\Model;
use Atk4\Ui\Form;
use Atk4\Ui\Header;

/**
 * BasicExecutor executor will typically fail if supplied arguments are not sufficient.
 *
 * ArgumentFormExecutor will ask user to fill in the blanks
 */
class ArgumentFormExecutor extends BasicExecutor
{
    /** @var Form */
    public $form;

    public function initPreview(): void
    {
        Header::addTo($this, [$this->action->getCaption(), 'subHeader' => $this->description ?? $this->action->getDescription()]);
        $this->form = Form::addTo($this, ['buttonSave' => $this->executorButton]);

        foreach ($this->action->args as $key => $val) {
            if ($val instanceof Model) {
                $val = ['model' => $val];
            }

            if (isset($val['model'])) {
                $val['model'] = Factory::factory($val['model']);
                $this->form->addControl($key, [Form\Control\Lookup::class])->setModel($val['model']);
            } else {
                $this->form->addControl($key, [], $val);
            }
        }

        $this->form->onSubmit(function (Form $form) {
            // set arguments from the model
            $this->setArguments($form->model->get());

            return $this->executeModelAction();
        });
    }
}
