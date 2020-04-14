<?php
/**
 * Loadable Interface.
 */

namespace atk4\ui\Panel;

interface Loadable
{
    /** Add loadable content to panel. */
    public function addDynamicContent(LoadableContent $content);

    /** Get panel loadable content. */
    public function getDynamicContent(): LoadableContent;
}
