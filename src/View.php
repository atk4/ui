<?php

namespace atk4\ui;

/**
 * Implements a most core view, which all of the other components descend
 * form. 
 */
class View {

    use \atk4\core\ContainerTrait {
        add as _add;
    }
    use \atk4\core\InitializerTrait {
        init as _init;
    }
    use \atk4\core\TrackableTrait;
    use \atk4\core\AppScopeTrait;

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
     * will output itself
     */
    public $region;


    /**
     * Enables UI keyword for Semantic UI indicating that this is a
     * UI element. If you set this variable value to string, it will
     * be appended at the end of the element class.
     */
    public $ui = false;

    /**
     * List of classes that needs to be added
     */
    public $class = [];

    /**
     * Just here temporarily, until App picks it up
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

    function __construct($defaults = []) {

        if (is_string($defaults)) {
            $this->content = $defaults;
            return;
        }

        if (!is_array($defaults)) {
            throw new Exception(['Constructor requires array argument', 'arg' => $defaults]);
        }

        $this->setProperties($defaults);
    }

    function setProperties($properties) {
        if(isset($properties[0])) {
            $this->content = $properties[0];
            unset($properties[0]);
        }
        foreach ($properties as $key => $val) {
            if(property_exists($this, $key)) {
                if (is_array($val)) {
                    $this->$key = array_merge(isset($this->$key) && is_array($this->$key) ? $this->$key : [], $val);
                } elseif(!is_null($val)) {
                    $this->$key = $val;
                }
            } else {
                $this->setProperty($key, $val);
            }
        }
    }

    function setProperty($key, $val) {

        if(is_numeric($key)) {
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
            'key'=>$key,
            'val'=>$val
        ]);
    }

    function init() {
        $this->_init();
        if(!$this->app) {
            $this->initDefaultApp();
        }

        if (is_string($this->template)) {
            $this->template = $this->app->loadTemplate($this->template);
        }
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

    public function removeClass($remove_class) {
        $remove_class = explode(' ', $remove_class);
        $this->class = array_diff($this->class, $remove_class);
    }


    /**
     * For the absence of the application, we would add a very
     * simple one
     */
    function initDefaultApp() {
        $this->app = new \atk4\ui\App(['skin'=>$this->skin]);
        $this->app->init();
    }

    /**
     * In addition to adding a child object, set up it's template
     * and associate it's output with the region in our template.
     */
    function add($object, $region = 'Content') {
        if(!$this->app) {
            $this->init();
            //$this->initDefaultApp();
        }
        $object = $this->_add($object);

        $object->region = $region;

        if(!$object->template) {
            $object->template = $this->template->cloneRegion($region);
            $this->template->del($region);
        }
        return $object;
    }

    function set($arg1 = [], $arg2 = null) {
        if (is_string($arg1) && !is_null($arg2)) {

            // must be initialized

            $this->template->set($arg1, $arg2);
            return $this;
        }

        if (!is_null($arg2)) {
            throw new Exception([
                'Second argument to set() can be only passed if the first one is a string',
                'arg1'=>$arg1,
                'arg2'=>$arg2,
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
            'arg1'=>$arg1
        ]);

        return $this;
    }

    function recursiveRender() {
        foreach($this->elements as $view) {
            if (!$view instanceof View) { 
                continue;
            }

            $this->template->appendHTML($view->region, $view->render());
        }

        if ($this->content) {
            $this->template->append('Content', $this->content);
        }

    }

    function renderView() {
        if ($this->class) {
            $this->template->append('class', join(' ', $this->class));
        }

        if ($this->ui) {
            if (is_string($this->ui)) {
                $this->template->set('_class', $this->ui);
            }
        } else {
            $this->template->tryDel('_ui');
        }
    }

    function render() {

        $this->renderView();

        $this->recursiveRender();
        
        return 
            $this->getJS().
            $this->template->render();
    }

    function setModel($m) {
        $this->model = $m;
    }

    function getHTML()
    {
        return $this->template->render();
    }

    function getJS()
    {
        return '';
    }

}
