<?php
/**
 * Flyable content interface.
 */

namespace atk4\ui\Panel;

use atk4\ui\jsCallback;

interface SlidableContent
{
    public function getWarningSelector() :string;

    public function getWarningTrigger() :string;

    public function setWarningSelector(string $selector);

    public function setWarningTrigger(string $trigger);

    public function setCb(jsCallback $cb);

    public function getCallbackUrl() :string;

    public function setCloseSelector(string $selector);

    public function getCloseSelector() :string;

    /**
     * Return an array of css class that should be
     * emptied when flyout reload.
     *
     * @return array
     */
    //public function getClearable() :array;

    /**
     * The callback for loading content.
     *
     * @param callable $callable
     */
    public function onLoad(callable $callable);

    /**
     * The content
     * @param bool $state
     */
    public function jsDisplayWarning(bool $state);
}