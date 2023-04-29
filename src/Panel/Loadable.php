<?php

declare(strict_types=1);

namespace Atk4\Ui\Panel;

interface Loadable
{
    /**
     * Add loadable content to panel.
     */
    public function addDynamicContent(LoadableContent $content): void;

    /**
     * Get panel loadable content.
     */
    public function getDynamicContent(): LoadableContent;
}
