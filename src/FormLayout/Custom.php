<?php

namespace atk4\ui\FormLayout;

/**
 * Custom Layout for a form (user-defined HTML).
 */
class Custom extends _Abstract
{
    // @var inheritdoc
    public $defaultTemplate = null;

    public function init()
    {
        parent::init();

        if (!$this->template) {
            throw new \atk4\ui\Exception(['You must specify template for FormLayout/Custom. Try [\'Custom\', \'defaultTemplate\'=>\'./yourform.html\']']);
        }
    }

    /**
     * Adds Button into {$Buttons}.
     *
     * @param Button|array|string $seed
     *
     * @return \atk4\ui\Button
     */
    public function addButton($seed)
    {
        return $this->add($this->mergeSeeds(['Button'], $seed), 'Buttons');
    }
}
