<?php

declare(strict_types=1);

namespace atk4\ui;

use atk4\core\AppScopeTrait;
use atk4\core\ContainerTrait;
use atk4\core\DiContainerTrait;
use atk4\core\FactoryTrait;
use atk4\core\InitializerTrait;
use atk4\core\StaticAddToTrait;
use atk4\core\TrackableTrait;

/**
 * Abstract view tree item (used only for View and Callback, you want probably to extend one of these).
 *
 * @property View $owner
 * @property View[] $elements
 */
abstract class AbstractView
{
    use ContainerTrait {
        add as private _add;
    }
    use InitializerTrait {
        init as private _init;
    }
    use TrackableTrait;
    use AppScopeTrait;
    use FactoryTrait;
    use DiContainerTrait;
    use StaticAddToTrait;

    /**
     * Default name of the element.
     *
     * @var string
     */
    public $defaultName = 'atk';

    /**
     * If add() method is called, but current view is not part of render tree yet,
     * then arguments to add() are simply stored in this array. When the view is
     * initialized by calling init() or adding into App or another initialized View,
     * then add() will be re-invoked with the contents of this array.
     *
     * @var array
     */
    protected $_add_later = [];

    /**
     * will be set to true after rendered. This is so that we don't render view twice.
     *
     * @var bool
     */
    protected $_rendered = false;

    // }}}

    // {{{ Default init() method and add() logic

    /**
     * For the absence of the application, we would add a very
     * simple one.
     */
    protected function initDefaultApp()
    {
        $this->app = new App([
            'skin' => $this->skin,
            'catch_exceptions' => false,
            'always_run' => false,
            'catch_runaway_callbacks' => false,
        ]);
        $this->app->init();
    }

    /**
     * Called when view becomes part of render tree. You can override it but avoid
     * placing any "heavy processing" here.
     */
    public function init(): void
    {
        if (!$this->app) {
            $this->initDefaultApp();
        }

        if ($this->name === null) {
            $this->name = $this->defaultName;
        }

        $this->_init();

        // add default objects
        foreach ($this->_add_later as [$object, $args]) {
            $this->add($object, $args);
        }
        $this->_add_later = [];
    }

    /**
     * @param AbstractView $object
     */
    public function add($object, $args = null): self
    {
        AbstractView::assertInstanceOf($object);

        if (func_num_args() > 2) { // prevent bad usage
            throw new \Error('Too many method arguments');
        } elseif ($this->_rendered) {
            throw new Exception('You cannot add anything into the view after it was rendered');
        }

        if (!$this->app) {
            $this->_add_later[] = [$object, $args];

            return $object;
        }

        // will call init() of the object
        $this->_add($object, $args);

        return $object;
    }

    // }}}

    // {{{ Sticky URLs

    /** @var string[] stickyGet arguments */
    public $stickyArgs = [];

    /**
     * Build an URL which this view can use for js call-backs. It should
     * be guaranteed that requesting returned URL would at some point call
     * $this->init().
     *
     * @param array $page
     *
     * @return string
     */
    public function jsUrl($page = [])
    {
        return $this->app->jsUrl($page, false, array_merge($this->_getStickyArgs($this->name), $this->stickyArgs));
    }

    /**
     * Build an URL which this view can use for call-backs. It should
     * be guaranteed that requesting returned URL would at some point call
     * $this->init().
     *
     * @param string|array $page URL as string or array with page name as first element and other GET arguments
     *
     * @return string
     */
    public function url($page = [])
    {
        return $this->app->url($page, false, array_merge($this->_getStickyArgs($this->name), $this->stickyArgs));
    }

    /**
     * Needed for tracking which view in a render tree called url().
     *
     * @internal
     */
    protected $_triggerBy;

    /**
     * Get sticky arguments defined by the view and parents (including API).
     *
     * @param string $triggerBy If exception occurs, will know which view called url()
     */
    protected function _getStickyArgs($triggerBy): array
    {
        $this->_triggerBy = $triggerBy;
        if ($this->owner && $this->owner instanceof self) {
            $stickyArgs = array_merge($this->owner->_getStickyArgs($triggerBy), $this->stickyArgs);
        } else {
            $stickyArgs = [];
        }

        return $stickyArgs;
    }

    /**
     * Mark GET argument as sticky. Calling url() on this view or any
     * sub-views will embedd the value of this GET argument.
     *
     * If GET argument is empty or false, it won't make into URL.
     *
     * If GET argument is not presently set you can specify a 2nd argument
     * to forge-set the GET argument for current view and it's sub-views.
     */
    public function stickyGet(string $name, string $newValue = null): ?string
    {
        $this->stickyArgs[$name] = $newValue ?? $_GET[$name] ?? null;

        return $this->stickyArgs[$name];
    }

    // }}}
}
