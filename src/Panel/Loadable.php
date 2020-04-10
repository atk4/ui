<?php
/**
 * Loadable Interface.
 */

namespace atk4\ui\Panel;

interface Loadable
{
    /** Add loadable content to panel. */
    function addDynamicContent(LoadableContent $content);

    /** Get panel loadable content. */
    function getDynamicContent() :LoadableContent;
}