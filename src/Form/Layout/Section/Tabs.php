<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Layout\Section;

use Atk4\Ui\Form;
use Atk4\Ui\Tabs as UiTabs;
use Atk4\Ui\View;

/**
 * Represents form controls in tabs.
 */
class Tabs extends UiTabs
{
    /** @var array */
    public $formLayoutSeed = [Form\Layout::class];

    public Form $form;

    /**
     * @return Form\Layout
     */
    #[\Override]
    public function addTab($name, ?\Closure $callback = null, array $settings = [])
    {
        $tab = parent::addTab($name, $callback, $settings);

        $res = View::fromSeed($this->formLayoutSeed, ['form' => $this->form]);
        $tab->add($res);

        return $res;
    }
}
