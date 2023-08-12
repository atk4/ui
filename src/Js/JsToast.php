<?php

declare(strict_types=1);

namespace Atk4\Ui\Js;

use Atk4\Core\DiContainerTrait;

/**
 * Create Fomantic-UI Toast using JS.
 *
 * Example output: $('body').toast({options}).
 */
class JsToast implements JsExpressionable
{
    use DiContainerTrait;

    /** @var array<string, mixed> Various setting options as per Fomantic-UI toast module. */
    public array $settings = [];

    /** @var string default CSS class for toast */
    public $defaultCss = 'success';

    /**
     * @param array<string, mixed>|string $settings
     */
    public function __construct($settings = null)
    {
        if (is_array($settings)) {
            $this->settings = $settings;
        } elseif (is_string($settings)) {
            $this->settings['message'] = $settings;
        }

        // set default CSS class
        if (!array_key_exists('class', $this->settings)) {
            $this->settings['class'] = $this->defaultCss;
        }
    }

    /**
     * Set message to display in Toast.
     *
     * @param string $msg
     *
     * @return $this
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
