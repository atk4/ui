<?php

declare(strict_types=1);

namespace atk4\ui\Form\Layout;

use atk4\ui\Exception;
use atk4\ui\Form\AbstractLayout;

/**
 * Custom Layout for a form (user-defined HTML).
 */
class Custom extends AbstractLayout
{
    /** @var {@inheritdoc} */
    public $defaultTemplate;

    protected function init(): void
    {
        parent::init();

        if (!$this->template) {
            throw new Exception('You must specify template for Form/Layout/Custom. Try [\'Custom\', \'defaultTemplate\'=>\'./yourform.html\']');
        }
    }

    /**
     * Adds Button into {$Buttons}.
     *
     * @param \atk4\ui\Button|array|string $seed
     *
     * @return \atk4\ui\Button
     */
    public function addButton($seed)
    {
        return $this->add($this->mergeSeeds([\atk4\ui\Button::class], $seed), 'Buttons');
    }
}
