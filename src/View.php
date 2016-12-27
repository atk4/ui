<?php

// vim:ts=4:sw=4:et:fdm=marker:fdl=0

namespace atk4\ui;

/**
 * Implements a most core view, which all of the other components descend
 * form.
 */
class View
{
    use \atk4\core\ContainerTrait {
        add as _add;
    }
    use \atk4\core\InitializerTrait {
        init as _init;
    }
    use \atk4\core\TrackableTrait;
    use \atk4\core\AppScopeTrait;

    // {{{ Properties of the class

    /**
     * When you call render() this will be populated with JavaScript
     * chains.
     *
     * @private! but must remain public so that child views could interact
     * with parent's $js.
     */
    public $js;

    public $model;

    /**
     * Name of the region in the parent's template where this object
     * will output itself.
     */
    public $region;

    /**
     * Enables UI keyword for Semantic UI indicating that this is a
     * UI element. If you set this variable value to string, it will
     * be appended at the end of the element class.
     */
    public $ui = false;

    /**
     * List of classes that needs to be added.
     */
    public $class = [];

    /**
     * Just here temporarily, until App picks it up.
     */
    protected $skin;

    /**
     * Path to template. If you don't specify full path
     * by starting with '/' then will be prepended by
     * default template path.
     */
    public $template = 'element.html';

    /**
     * Set static contents of this view.
     */
    public $content = null;

    // }}}

    // {{{ Setting Things up

    /**
     * May accept properties of a class, but if property is not defined, it will
     * be used as a HTML class instead.
     */
    public function __construct($defaults = [])
    {
        if (is_string($defaults)) {
            $this->content = $defaults;

            return;
        }

        if (!is_array($defaults)) {
            throw new Exception(['Constructor requires array argument', 'arg' => $defaults]);
        }

        $this->setProperties($defaults);
    }

    /**
     * Associate this view with a model. Do not place any logic in this class, instead take it
     * to renderView().
     *
     * Do not try to create your own "Model" implementation, instead you must be looking for
     * your own "Persistence" implementation.
     */
    public function setModel(\atk4\data\Model $m)
    {
        $this->model = $m;

        return $m;
    }

    /**
     * Called from __consruct() and set() to initialize teh properties.
     *
     * TODO: move into trait, because this is used often
     */
    public function setProperties($properties)
    {
        if (isset($properties[0])) {
            $this->content = $properties[0];
            unset($properties[0]);
        }
        foreach ($properties as $key => $val) {
            if (property_exists($this, $key)) {
                if (is_array($val)) {
                    $this->$key = array_merge(isset($this->$key) && is_array($this->$key) ? $this->$key : [], $val);
                } elseif (!is_null($val)) {
                    $this->$key = $val;
                }
            } else {
                $this->setProperty($key, $val);
            }
        }
    }

    /**
     * TODO: move into trait because it's used so often.
     */
    public function setProperty($key, $val)
    {
        if (is_numeric($key)) {
            $key = $val;
            $val = true;
        }

        if ($val === true) {
            $this->addClass($key);

            return;
        } elseif ($val === false) {
            $this->removeClass($key);

            return;
        }

        throw new Exception([
            'Not sure what to do',
            'key'=> $key,
            'val'=> $val,
        ]);
    }

    // }}}

    // {{{ Default init() method and add() logic

    /**
     * Called when view becomes part of render tree. You can override it but avoid
     * placing any "heavy processing" here.
     */
    public function init()
    {
        $this->_init();
        if (!$this->app) {
            $this->initDefaultApp();
        }

        if (is_string($this->template)) {
            $this->template = $this->app->loadTemplate($this->template);
        }
    }

    /**
     * For the absence of the application, we would add a very
     * simple one.
     */
    public function initDefaultApp()
    {
        $this->app = new \atk4\ui\App(['skin'=>$this->skin]);
        $this->app->init();
    }

    /**
     * In addition to adding a child object, set up it's template
     * and associate it's output with the region in our template.
     */
    public function add($object, $region = 'Content')
    {
        if (!$this->app) {
            $this->init();
            //$this->initDefaultApp();
        }
        $object = $this->_add($object);

        $object->region = $region;

        if (!$object->template) {
            $object->template = $this->template->cloneRegion($region);
            $this->template->del($region);
        }

        return $object;
    }

    // }}}

    // {{{ Manipulating classes and view properties

    /**
     * Override this method without compatibility with parrent, if you wish
     * to set your own things your own way for your view.
     */
    public function set($arg1 = [], $arg2 = null)
    {
        if (is_string($arg1) && !is_null($arg2)) {

            // must be initialized

            $this->template->set($arg1, $arg2);

            return $this;
        }

        if (!is_null($arg2)) {
            throw new Exception([
                'Second argument to set() can be only passed if the first one is a string',
                'arg1'=> $arg1,
                'arg2'=> $arg2,
            ]);
        }

        if (is_string($arg1)) {
            $this->content = $arg1;

            return $this;
        }

        if (is_array($arg1)) {
            $this->setProperties($arg1);

            return $this;
        }

        throw new Exception([
            'Not sure what to do with argument',
            'arg1'=> $arg1,
        ]);

        return $this;
    }

    /**
     * Add CSS class to element. Previously added classes are not affected.
     * Multiple CSS classes can also be added if passed as space separated
     * string or array of class names.
     *
     * @param string|array $class CSS class name or array of class names
     *
     * @return $this
     */
    public function addClass($class)
    {
        if (is_array($class)) {
            $class = implode(' ', $class);
        }

        $this->class = array_merge($this->class, explode(' ', $class));

        return $this;
    }

    /**
     * Remove one or several CSS classes from the element.
     *
     * @param string|array $class CSS class name or array of class names
     *
     * @return $this
     */
    public function removeClass($remove_class)
    {
        $remove_class = explode(' ', $remove_class);
        $this->class = array_diff($this->class, $remove_class);
    }

    // }}}

    // {{{ Rendering

    /**
     * View-specific rendering stuff. Feel free to replace this method with
     * your own. View::renderView contanis some logic that integrates with
     * semanticUI.
     *
     * NOTE: maybe in the future, SemanticUI-related stuff needs to go into
     * a separate class.
     */
    public function renderView()
    {
        if ($this->class) {
            $this->template->append('class', implode(' ', $this->class));
        }

        if ($this->ui) {
            if (is_string($this->ui)) {
                $this->template->set('_class', $this->ui);
            }
        } else {
            $this->template->tryDel('_ui');
        }
    }

    /**
     * Recursively render all children, placing their
     * output in our template.
     */
    public function recursiveRender()
    {
        foreach ($this->elements as $view) {
            if (!$view instanceof self) {
                continue;
            }

            $this->template->appendHTML($view->region, $view->render());
        }

        if ($this->content) {
            $this->template->append('Content', $this->content);
        }
    }

    /**
     * This method is for those cases when developer want to simply render his
     * view and grab HTML himself.
     */
    public function render()
    {
        $this->renderView();

        $this->recursiveRender();

        return
            $this->getJS().
            $this->template->render();
    }

    /**
     * TODO: refactor.
     */
    public function getHTML()
    {
        return $this->template->render();
    }

    /**
     * TODO: refactor.
     */
    public function getJS()
    {
        return '';
    }

    // }}}
}
