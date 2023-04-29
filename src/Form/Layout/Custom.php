<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Layout;

use Atk4\Core\Factory;
use Atk4\Ui\Button;
use Atk4\Ui\Exception;
use Atk4\Ui\Form;

class Custom extends Form\AbstractLayout
{
    public $defaultTemplate;

    protected function init(): void
    {
        parent::init();

        if (!$this->template) {
            throw new Exception('You must specify template for Form/Layout/Custom. Try [\'Custom\', \'defaultTemplate\' => \'./yourform.html\']');
        }
    }

    /**
     * Adds Button into {$Buttons}.
     *
     * @param Button|array $seed
     *
     * @return Button
     */
    public function addButton($seed)
    {
        return $this->add(Factory::mergeSeeds([Button::class], $seed), 'Buttons');
    }
}
