<?php

namespace atk4\ui\FormLayout\Section;

class Tabs extends \atk4\ui\Tabs
{
    public $formLayout = \atk4\ui\FormLayout\Generic::class;
    public $form;

    /**
     * Adds tab in tabs widget.
     *
     * @param string|Tab $name     Name of tab or Tab object
     * @param callable   $callback Callback action or URL (or array with url + parameters)
     *
     * @throws Exception
     *
     * @return \atk4\ui\FormLayout\Generic
     */
    public function addTab($name, $callback = null)
    {
        $c = parent::addTab($name, $callback);

        return $c->add([$this->formLayout, 'form' => $this->form]);
    }
}
