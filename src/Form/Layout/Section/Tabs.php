<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Layout\Section;

use Atk4\Ui\Form;
use Atk4\Ui\Tabs as UiTabs;
use Atk4\Ui\TabsTab;

/**
 * Represents form controls in tabs.
 */
class Tabs extends UiTabs
{
    /** @var class-string<Form\Layout> */
    public $formLayout = Form\Layout::class;

    public Form $form;

    /**
     * @param string|TabsTab $name
     * @param \Closure       $callback Callback action or URL (or array with url + parameters)
     *
     * @return Form\Layout
     */
    public function addTab($name, \Closure $callback = null, array $settings = [])
    {
        $tab = parent::addTab($name, $callback, $settings);

        return $tab->add([$this->formLayout, 'form' => $this->form]);
    }
}
