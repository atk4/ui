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
     * Adds Button into {$Buttons}
     *
     * @param array|string $button
     *
     * @return \atk4\ui\Button
     */
    public function addButton($button)
    {
        return $this->add($this->mergeSeeds(['Button'], $button), 'Buttons');
    }
}
