<?php

namespace atk4\ui\Layout;

/**
 * Implements a fixed-width single-column bevel in the middle of the page, centered
 * horizontally and vertically. Icon / Title will apear above the bevel.
 *
 * Bevel will use some padding and will contain your Content.
 * This layout is handy for a simple and single-purpose applications.
 */
class Centered extends Generic
{
    use \atk4\core\DebugTrait;

    public $defaultTemplate = 'layout/centered.html';

    // Default atk4 logo
    public $image = 'https://github.com/atk4/ui/raw/07208a0af84109f0d6e3553e242720d8aeedb784/public/logo.png';
    public $image_alt = 'Logo';

    public function init()
    {
        parent::init();

        // set application's title

        $this->template->trySet('title', $this->app->title);
    }

    public function renderView() {
        if ($this->image) {
            $this->template->trySetHTML('HeaderImage', '<img class="ui image" src="'.$this->image.'" alt="'.$this->image_alt.'" />');
        }
        parent::renderView();
    }
}
