<?php

// vim:ts=4:sw=4:et:fdm=marker:fdl=0

namespace atk4\ui;

/**
 * Implements a most core view, which all of the other components descend
 * form.
 */
class View implements jsExpressionable
{
    use \atk4\core\ContainerTrait {
        add as _add;
    }
    use \atk4\core\InitializerTrait {
        init as _init;
    }
    use \atk4\core\TrackableTrait;
    use \atk4\core\AppScopeTrait;
    use \atk4\core\FactoryTrait;

    // {{{ Properties of the class

    /**
     * When you call render() this will be populated with JavaScript
     * chains.
     *
     * @private! but must remain public so that child views could interact
     * with parent's $js.
     */
    public $_js_actions = [];

    public $model;

    /**
     * Name of the region in the parent's template where this object
     * will output itself.
     */
    public $region = 'Content';

    /**
     * Enables UI keyword for Semantic UI indicating that this is a
     * UI element. If you set this variable value to string, it will
     * be appended at the end of the element class.
     */
    public $ui = false;

    /**
     * ID of the element, that's unique and is used in JS operations.
     */
    public $id = null;

    /**
     * List of classes that needs to be added.
     */
    public $class = [];

    /**
     * Just here temporarily, until App picks it up.
     */
    protected $skin;

    /**
     * Template object, that, for most Views will be rendered to
     * produce HTML output. If you leave this object as "null" then
     * a new Template will be generated during init() based on the
     * value of $defaultTemplate.
     *
     * @var Template
     */
    public $template = null;

    /**
     * Specifies how to initialize $template.
     *
     * If you specify a string, then it will be considered a filename
     * from which to load the $template.
     *
     * @var string
     */
    public $defaultTemplate = 'element.html';

    /**
     * Set static contents of this view.
     */
    public $content = null;

    /**
     * Change this if you want to substitute default "div" for something else.
     */
    public $element = null;

    // }}}

    // {{{ Setting Things up

    /**
     * May accept properties of a class, but if property is not defined, it will
     * be used as a HTML class instead.
     *
     * @param array $defaults
     *
     * @throws Exception
     */
    public function __construct($defaults = [])
    {
        if (is_string($defaults) && $this->content !== false) {
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
     *
     * @param \atk4\data\Model $m
     *
     * @return Model
     */
    public function setModel(\atk4\data\Model $model)
    {
        $this->model = $model;

        return $model;
    }

    public function setSource(array $data)
    {
        $goodData = [];

        foreach ($data as $key=>$value) {
            if (!is_array($value)) {
                $value = ['name'=>$value];
            }

            if (!isset($value['id'])) {
                $value['id'] = $key;
            }
            $goodData[] = $value;
        }
        $goodData = ['data'=>$goodData];

        $model = new \atk4\data\Model(
            new \atk4\data\Persistence_Array($goodData), 'data'
        );
        $model->addField('name');

        return $this->setModel($model);
    }

    /**
     * Called from __consruct() and set() to initialize teh properties.
     *
     * TODO: move into trait, because this is used often
     *
     * @param $properties
     */
    protected function setProperties($properties)
    {
        if (isset($properties[0]) && $this->content !== false) {
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
     *
     * @param $key
     * @param $val
     *
     * @throws Exception
     */
    protected function setProperty($key, $val)
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
        if (!$this->name) {
            if (!$this->id) {
                $this->id = $this->name = 'atk';
            } else {
                $this->name = $this->id;
            }
        } elseif (!$this->id) {
            $this->id = $this->name;
        }

        $this->_init();

        if (!$this->app) {
            $this->initDefaultApp();
        }

        if (is_string($this->defaultTemplate) && is_null($this->template)) {
            $this->template = $this->app->loadTemplate($this->defaultTemplate);
        }
    }

    /**
     * For the absence of the application, we would add a very
     * simple one.
     */
    protected function initDefaultApp()
    {
        $this->app = new App(['skin'=>$this->skin]);
        $this->app->init();
    }

    /**
     * In addition to adding a child object, set up it's template
     * and associate it's output with the region in our template.
     *
     * @param View|strin   $object New object to add
     * @param string|array $region (or array for full set of defaults)
     *
     * @return View
     */
    public function add($object, $region = null)
    {
        if (!$this->app) {
            $this->init();
        }

        if ($region === null) {
            $defaults = ['region' => 'Content'];
        } elseif (!is_array($region)) {
            $defaults = ['region' => $region];
        } else {
            $defaults = $region;
            if (isset($defaults[0])) {
                $defaults['region'] = $defaults[0];
                unset($defaults[0]);
            }
        }

        $object = $this->_add($object, $defaults);

        if (!$object->template && $object->region) {
            $object->template = $this->template->cloneRegion($object->region);
            $this->template->del($object->region);
        }

        return $object;
    }

    // }}}

    // {{{ Manipulating classes and view properties

    /**
     * Override this method without compatibility with parrent, if you wish
     * to set your own things your own way for your view.
     *
     * @param array       $arg1
     * @param string|null $arg2
     *
     * @throws Exception
     *
     * @return $this
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
     * @param array|string $class CSS class name or array of class names
     *
     * @return $this
     */
    public function removeClass($class)
    {
        if (is_array($class)) {
            $class = implode(' ', $class);
        }

        $class = explode(' ', $class);
        $this->class = array_diff($this->class, $class);

        return $this;
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

        if ($this->id) {
            $this->template->trySet('_id', $this->id);
        }

        if ($this->element) {
            $this->template->set('_element', $this->element);
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

            $this->template->appendHTML($view->region, $view->getHTML());

            if ($view->_js_actions) {
                $this->_js_actions = array_merge_recursive($this->_js_actions, $view->_js_actions);
            }
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
        if (!$this->_initialized) {
            $this->init();
        }

        $this->renderView();

        $this->recursiveRender();

        return
            $this->getJS().
            $this->template->render();
    }

    /**
     * Created for recursive rendering or when you want to only get HTML of this object (not javascript).
     */
    public function getHTML()
    {
        if (!$this->_initialized) {
            $this->init();
        }

        $this->renderView();

        $this->recursiveRender();

        return $this->template->render();
    }

    // }}}

    // {{{ JavaScript integration

    /**
     * Views in Agile UI can assign javascript actions to themselves. This
     * is done by calling $view->js() method which returns instance of jsChain
     * object that is initialized to the object itself. Normally this chain
     * will map into $('#object_id') and calling additional methods will map
     * into additional calls.
     *
     * Action can represent javascript event, such as "click" or "mouseenter".
     * If you specify action = true, then the event will ALWAYS be executed on
     * documentReady. It will also be executed if respective view is being reloaded
     * by js()->reload()
     *
     * (Do not make mistake by specifying "true" instead of true)
     *
     * action = false will still return jsChain but will not bind it.
     * You can bind it by passing object into on() method.
     *
     * 1. Calling with arguments:
     *
     * $view->js();                   // technically does nothing
     * $a = $view->js()->hide();      // creates chain for hiding $view but does not
     *                                // bind to event yet.
     *
     * 2. Binding existing chains
     * $img->on('mouseenter', $a);    // binds previously defined chain to event on
     *                                // event of $img.
     *
     * Produced code: $('#img_id').on('mouseenter', function(ev){ ev.preventDefault();
     *    $('#view1').hide(); });
     *
     * 3. $button->on('click',$form->js()->submit());
     *                                // clicking button will result in form submit
     *
     * 4. $view->js(true)->find('.current')->text($text);
     *
     * Will convert calls to jQuery chain into JavaScript string:
     *  $('#view').find('.current').text('abc');    // The $text will be json-encoded
     *                                              // to avoid JS injection.
     *
     * Documentation:
     *
     * @link http://agile-ui.readthedocs.io/en/latest/js.html
     *
     * @param string|bool|null $when Event when chain will be executed
     *
     * @return jQuery
     */
    public function js($when = null, $extra = null)
    {
        $chain = new jQuery($this);

        // Substitute $when to make it better work as a array key
        if ($when === true) {
            $when = 'always';
        }

        if ($when === false || $when === null) {
            return $chain;
        }

        if (!isset($this->_js_actions[$when])) {
            $this->_js_actions[$when] = [];
        }

        $this->_js_actions[$when][] = $chain;

        if ($extra) {
            $this->_js_actions[$when][] = $extra;
        }

        return $chain;
    }

    /**
     * Views in Agile Toolkit can assign javascript actions to themselves. This
     * is done by calling $view->js() or $view->on().
     *
     * on() method is similar to jQuery on() method.
     *
     * on(event, [selector,] action)
     *
     * Method on() also returns a chain, that will correspond affected element.
     * Here are some ways to use on();
     *
     * $button->on('click', $view->js()->hide());
     *
     *   // clicking on button will make the $view dissapear
     *
     * $view->on('click', 'a[data=clickable]')->parent()->hide();
     *
     *   // clicking on <a class="clickable"> will make it's parent dissapear
     *
     * Finally, it's also possible to use PHP closure as an action:
     *
     * $view->on('click', 'a', function($js, $data){
     *   if (!$data['clickable']) {
     *      return new jsExpression('alert([])', ['This record is not clickable'])
     *   }
     *   return $js->parent()->hide();
     * });
     *
     * For more information on how this works, see documentation:
     *
     * @link http://agile-ui.readthedocs.io/en/latest/js.html
     *
     * @param string           $event    JavaScript event
     * @param string           $selector Optional jQuery-style selector
     * @param jsChain|callable $action   code to execute
     *
     * @throws Exception
     *
     * @return jQuery
     */
    public function on($event, $selector = null, $action = null)
    {
        // second argument may be omitted
        if (!is_string($selector) && is_null($action)) {
            $action = $selector;
            $selector = null;
        }

        $actions = [];

        // will be returned from this method, so you can chain more stuff on it
        $actions[] = $thisAction = new jQuery(new jsExpression('this'));

        if (is_callable($action)) {
            // if callable $action is passed, then execute ajaxec()

            // create callback, that will include event as part of the full name
            $this->_add($cb = new Callback(), ['desired_name'=>$event]);

            $cb->set(function () use ($action) {
                $chain = new jQuery(new jsExpression('this'));
                $response = call_user_func($action, $chain);

                if ($response === $chain) {
                    $response = null;
                }

                $actions = [];

                if ($chain->_chain) {
                    $actions[] = $chain;
                }

                if (!is_array($response)) {
                    $response = [$response];
                }

                foreach ($response as $r) {
                    if (is_string($r)) {
                        $actions[] = new jsExpression('alert([])', [r]);
                    } elseif ($r instanceof jsExpressionable) {
                        $actions[] = $r;
                    } elseif ($r === null) {
                        continue;
                    } else {
                        throw new Exception(['Incorrect callback. Must be string or action.', 'r'=>$r]);
                    }
                }

                $ajaxec = implode(";\n", array_map(function (jQuery $r) {
                    return $r->jsRender();
                }, $actions));

                echo json_encode(['success'=>true, 'message'=>'Hello World', 'eval'=>$ajaxec]);
                exit;
            });

            $thisAction->api(['on'=>'now', 'url'=>$cb->getURL(), 'obj'=>new jsExpression('this')]);

            //throw new Exception('VirtualPage is not yet implemented');
            /*$url = '.virtualpage->getURL..';
            $actions[] = (new jsUniv(new jsExpression('this')))->ajaxec($url, true);

            /*
            $p = $this->add('VirtualPage');

            $p->set(function ($p) use ($action) {
                // $action is an actual callable
                $js2 = $p->js()->_selectorRegion();

                $js3 = call_user_func($action, $js2, $_POST);

                // If method returns something, execute that instead
                if ($js3) {
                    $p->js(null, $js3)->execute();
                } else {
                    $js2->execute();
                }
            });

            $action = $this->js()->_selectorThis()->univ()->ajaxec($p->getURL(), true);
             */
        } elseif ($action) {
            // otherwise include
            $actions[] = $action;
        }

        $actions['preventDefault'] = true;
        $actions['stopPropagation'] = true;

        $action = new jsFunction($actions);

        if ($selector) {
            $this->js(true)->on($event, $selector, $action);
        } else {
            $this->js(true)->on($event, $action);
        }

        return $thisAction;

        /*
        if ($js) {
            $ret_js = $this->js(null, $js)->_selectorThis();
        } else {
            $ret_js = $this->js()->_selectorThis();
        }

        $on_chain = $this->js(true);
        $fired = false;

        $this->app->jui->addHook(
            'pre-getJS',
            function ($app) use ($event, $selector, $ret_js, $on_chain, &$fired) {
                if ($fired) {
                    return;
                }
                $fired = true;

                $on_chain->on($event, $selector, $ret_js->_enclose(null, true));
            }
        );

        return $ret_js;
         */
    }

    public function jsRender()
    {
        if (!$this->_initialized) {
            throw new Exception('Render tree must be initialized before materializing jsChains.');
        }

        return json_encode('#'.$this->id);
    }

    /**
     * TODO: refactor.
     */
    public function getJS()
    {
        $actions = [];

        foreach ($this->_js_actions as $event=>$eventActions) {
            foreach ($eventActions as $action) {
                // wrap into callback
                if ($event !== 'always') {
                    $action = (new jQuery($action->_constructorArgs[0]))
                        ->bind($event, new jsFunction([$action, 'preventDefault'=>true, 'stopPropagation'=>true]));
                }

                $actions[] = $action;
            }
        }

        if (!$actions) {
            return '';
        }

        $actions['indent'] = '';

        $ready = new jsFunction($actions);

        return "<script>\n".
            (new jQuery($ready))->jsRender().
            '</script>';
    }

    // }}}
}
