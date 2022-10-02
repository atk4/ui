<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Core\DiContainerTrait;

/**
 * Generate a Fomantic-UI toast module command in js.
 *  $('body').toast({options}).
 */
class JsToast implements JsExpressionable
{
    use DiContainerTrait;

    /** @var array Various setting options as per Fomantic-UI toast module. */
    public array $settings = [];

    /** @var string default css class for toast */
    public $defaultCss = 'success';

    /**
     * @param array|string $settings
     */
    public function __construct($settings = null)
    {
        if (is_array($settings)) {
            $this->settings = $settings;
        } elseif (is_string($settings)) {
            $this->settings['message'] = $settings;
        }

        // set defautl css class.
        if (!array_key_exists('class', $this->settings)) {
            $this->settings['class'] = $this->defaultCss;
        }
    }

    /**
     * Set message to display in Toast.
     *
     * @param string $msg
     */
    public function setMessage($msg): self
    {
        $this->settings['message'] = $msg;

        return $this;
    }

    public function jsRender(): string
    {
        return (new Jquery('body'))->toast($this->settings)->jsRender();
    }
}
