<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Layout;

use Atk4\Core\Factory;
use Atk4\Ui\Exception;
use Atk4\Ui\Form\AbstractLayout;

/**
 * Custom Layout for a form (user-defined HTML).
 */
class Custom extends AbstractLayout
{
    /** @var string */
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
     * @param \Atk4\Ui\Button|array|string $seed
     *
     * @return \Atk4\Ui\Button
     */
    public function addButton($seed)
    {
        return $this->add(Factory::mergeSeeds([\Atk4\Ui\Button::class], $seed), 'Buttons');
    }
}
