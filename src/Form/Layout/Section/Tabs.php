<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Layout\Section;

/**
 * Represents form controls in tabs.
 */
class Tabs extends \Atk4\Ui\Tabs
{
    public $formLayout = \Atk4\Ui\Form\Layout::class;
    public $form;

    /**
     * Adds tab in tabs widget.
     *
     * @param string|\Atk4\Ui\Tab $name     Name of tab or Tab object
     * @param \Closure            $callback Callback action or URL (or array with url + parameters)
     * @param array               $settings tab settings
     *
     * @return \Atk4\Ui\Form\Layout
     */
    public function addTab($name, \Closure $callback = null, $settings = [])
    {
        $tab = parent::addTab($name, $callback, $settings);

        return $tab->add([$this->formLayout, 'form' => $this->form]);
    }
}
