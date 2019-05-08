<?php
namespace atk4\ui\ActionExecutor;

use atk4\ui\Exception;
use atk4\ui\Form;

/**
 * Basic executor will typically fail if supplied arguments are not sufficient.
 *
 * ArgumentForm will ask user to fill in the blanks
 */

class ArgumentForm extends Basic
{

    /**
     * @var Form
     */
    public $form;

    /**
     * Initialization.
     */
    public function initPreview()
    {
        /*
         * We might want console later!
         *
        $this->console = $this->add(['Console', 'event'=>false]);//->addStyle('display', 'none');
        $this->console->addStyle('max-height', '50em')->addStyle('overflow', 'scroll');

        */

        $this->add(['Header', $this->action->caption, 'subHeader'=>$this->action->getDescription()]);
        $this->form = $this->add('Form');

        foreach($this->action->args as $key=>$val) {
            if (is_numeric($key)) {
                throw new Exception(['Action arguments must be named', 'args'=>$this->actions->args]);
            }

            if ($val instanceof \atk4\data\Model) {
                $this->form->addField($key, ['AutoComplete'])->setModel($val);
            } else {
                $this->form->addField($key, null, $val);
            }
        }

        $this->form->buttonSave->set('Run');

        $this->form->onSubmit(function($f) {

            // set arguments from the model
            $this->setArguments($f->model->get());

            return $this->jsExecute();;

            //return [$this->console->js()->show(), $this->console->sse];
        });

        /*
        $this->console->set(function($c) {
            $data = $this->recall('data');
            $args = [];

            foreach($this->defs as $key=>$val) {
                if (is_numeric($key)) {
                    $key = 'Argument'.$key;
                }

                if (is_callable($val)) {
                    $val = $val($this->model, $this->method, $data);
                } elseif ($val instanceof \atk4\data\Model) {
                    $val->load($data[$key]);
                } else {
                    $val = $data[$key];
                }

                $args[] = $val;
            }

            $c->setModel($this->model, $this->method, $args);
        });
        */
    }
}
