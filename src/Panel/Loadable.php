<?php
/**
 * Slidable Interface.
 */

namespace atk4\ui\Panel;

interface Loadable
{

    public function addDynamicContent(LoadableContent $content);

    public function getDynamicContent() :LoadableContent;
}