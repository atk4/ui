<?php

namespace atk4\ui\FormField;

use atk4\ui\Form;

/**
 * Input element for a form field.
 */
class Calendar extends Input
{
    /**
     * Set this to 'date', 'time', 'month' or 'year'. Leaving this blank
     * will show both date and time.
     */
    public $type = null;

    /**
     * Any other options you'd like to pass to calendar  JS.
     */
    public $options = [];

    public function renderView()
    {
        if (!$this->icon) {
            switch ($this->type) {
            //case 'date': $this->icon = '
            }
        }

        if ($this->type) {
            $this->options['type'] = $this->type;
        }

        $this->js(true)->calendar($this->options);

        parent::renderView();
    }
}
