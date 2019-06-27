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

    /**
     * @see \atk4\ui\App::$cdn
     *
     * @var null|string
     */
    public $image = null;
    public $image_alt = 'Logo';

    public function init()
    {
        parent::init();

        // If image is still unset load it when layout is initialized from the App
        if ($this->image === null && $this->app) {
            if (isset($this->app->cdn['layout-logo']))
                $this->image = $this->app->cdn['layout-logo'];
            else
                $this->image = $this->app->cdn['atk'] . '/logo.png';
        }

        // set application's title

        $this->template->trySet('title', $this->app->title);
    }

    public function renderView()
    {
        if ($this->image) {
            $this->template->trySetHTML('HeaderImage', '<img class="ui image" src="'.$this->image.'" alt="'.$this->image_alt.'" />');
        }
        parent::renderView();
    }
}
