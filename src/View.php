<?php

namespace atk4\ui;

/**
 * Implements a generic view. Can be used and will produce a single DIV.
 */
class View {

    use \atk4\core\ContainerTrait {
        add as _add;
    }
    use \atk4\core\InitializerTrait;
    use \atk4\core\TrackableTrait;
    use \atk4\core\AppScopeTrait;

    public $template = null;

    /**
     * When you call render() this will be populated with JavaScript
     * chains.
     */
    public $js;

    public $model;

    /**
     * Relative path to a template
     */
    public $default_template = null;

    function __construct($template = null) {

        if ($template) {
            $this->template = new Template();
            $this->template->load($template);
        } elseif ($this->default_template) {

            var_Dump(dirname(dirname(__FILE__)));

            $this->template = new Template();
            $this->template->load($template);

        }
    }

    function add($object, $spot = 'Content') {
        $object = $this->_add($object);

        $object->spot = $spot;


        if(!$object->template) {
            $object->template = $this->template->cloneRegion($spot);
            $this->template->del($spot);
        }
        return $object;
    }

    function recursiveRender() {
        foreach($this->elements as $view) {
            if (!$view instanceof View) { 
                continue;
            }

            $this->template->appendHTML($view->spot, $view->render());
        }
    }

    function render() {

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
        return $this->output;
    }

    function getJS()
    {
        return '';
    }

}
