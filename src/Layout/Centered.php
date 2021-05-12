<?php

declare(strict_types=1);

namespace Atk4\Ui\Layout;

/**
 * Implements a fixed-width single-column bevel in the middle of the page, centered
 * horizontally and vertically. Icon / Title will apear above the bevel.
 *
 * Bevel will use some padding and will contain your Content.
 * This layout is handy for a simple and single-purpose applications.
 */
class Centered extends \Atk4\Ui\Layout
{
    use \Atk4\Core\DebugTrait;

    public $defaultTemplate = 'layout/centered.html';

    /**
     * @see \Atk4\Ui\App::$cdn
     *
     * @var string|null
     */
    public $image;
    public $image_alt = 'Logo';

    protected function init(): void
    {
        parent::init();

        // If image is still unset load it when layout is initialized from the App
        if ($this->image === null && $this->issetApp()) {
            if (isset($this->getApp()->cdn['layout-logo'])) {
                $this->image = $this->getApp()->cdn['layout-logo'];
            } else {
                $this->image = $this->getApp()->cdn['atk'] . '/logo.png';
            }
        }

        // set application's title

        $this->template->trySet('title', $this->getApp()->title);
    }

    protected function renderView(): void
    {
        if ($this->image) {
            $this->template->tryDangerouslySetHtml('HeaderImage', '<img class="ui image" src="' . $this->image . '" alt="' . $this->image_alt . '" />');
        }
        parent::renderView();
    }
}
