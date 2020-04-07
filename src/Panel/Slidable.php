<?php
/**
 * Slidable Interface.
 */

namespace atk4\ui\Panel;

interface Slidable
{

    public function addPanelContent(SlidableContent $content);

    public function getSlideContent() :SlidableContent;
}