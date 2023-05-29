<?php

declare(strict_types=1);

namespace Atk4\Ui;

class Icon extends View
{
    public $defaultTemplate = 'icon.html';

    public $content = 'circle help';

    protected function renderView(): void
    {
        $this->addClass($this->content . ' icon');
        $this->content = null;

        parent::renderView();
    }
}
