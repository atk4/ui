<?php

declare(strict_types=1);

namespace atk4\ui\Form\Layout\Section;

/**
 * Represents form fields in tabs.
 */
class Tabs extends \atk4\ui\Tabs
{
    public $formLayout = \atk4\ui\Form\Layout::class;
    public $form;

    /**
     * Adds tab in tabs widget.
     *
     * @param string|\atk4\ui\Tab $name     Name of tab or Tab object
     * @param callable            $callback Callback action or URL (or array with url + parameters)
     * @param callable            $settings tab settings
     *
     * @return \atk4\ui\Form\Layout
     */
    public function addTab($name, $callback = null, $settings = [])
    {
        $tab = parent::addTab($name, $callback, $settings);

        return $tab->add([$this->formLayout, 'form' => $this->form]);
    }
}
