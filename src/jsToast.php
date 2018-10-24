<?php

namespace atk4\ui;
/**
 * Class jsToast
 * Generate a Fomantic-ui toast module command in js.
 *  $('body').toast({options})
 *
 * @package atk4\ui
 */

class jsToast implements jsExpressionable
{
    /**
     * Various setting options as per Fomantic ui toast module
     *
     * @var array|string
     */
    public $settings = [];

    public function __construct($settings = null)
    {
        if ($settings && is_array($settings)) {
            $this->settings = $settings;
        } elseif (is_string($settings)) {
            $this->settings['message'] = $settings;
        }
    }

    public function jsRender()
    {
        return (new jQuery('body'))->toast($this->settings)->jsRender();
    }
}
