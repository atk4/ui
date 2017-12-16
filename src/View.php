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
    use \atk4\core\DIContainerTrait {
        setMissingProperty as _setMissingProperty;
    }

    // {{{ Properties of the class

    /**
     * When you call render() this will be populated with JavaScript
     * chains.
     *
     * @private! but must remain public so that child views could interact
     * with parent's $js.
     *
     * @var array
     */
    public $_js_actions = [];

    /**
     * Data model.
     *
     * @var \atk4\data\Model
     */
    public $model;

    /**
     * Name of the region in the parent's template where this object
     * will output itself.
     *
     * @var string
     */
    public $region = null; //'Content';

    /**
     * Enables UI keyword for Semantic UI indicating that this is a
     * UI element. If you set this variable value to string, it will
     * be appended at the end of the element class.
     *
     * @var bool
     */
    public $ui = false;

    /**
     * ID of the element, that's unique and is used in JS operations.
     *
     * @var string
     */
    public $id = null;

    /**
     * List of classes that needs to be added.
     *
     * @var array
     */
    public $class = [];

    /**
     * List of custom CSS attributes.
     *
     * @var array
     */
    public $style = [];

    /**
     * List of custom attributes.
     *
     * @var array
     */
    public $attr = [];

    /**
     * Just here temporarily, until App picks it up.
     *
     * @var string
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
     *
     * @var string|false
     */
    public $content = null;

    /**
     * Change this if you want to substitute default "div" for something else.
     *
     * @var string
     */
    public $element = null;

    // @var array
    protected $_add_later = [];

    // will be set to true after render
    protected $_rendered = false;

    // }}}

    // {{{ Setting Things up

    /**
     * May accept properties of a class, but if property is not defined, it will
     * be used as a HTML class instead.
     *
     * @param array|string $label
     * @param array|string $class
     *
     * @throws Exception
     */
    public function __construct($label = null, $class = null)
    {
        if (is_array($label)) {
            // backwards mode
            $defaults = $label;
            if (isset($defaults[0])) {
                $label = $defaults[0];
                unset($defaults[0]);
            } else {
                $label = null;
            }

            if (isset($defaults[1])) {
                $class = $defaults[1];
                unset($defaults[1]);
            }
            $this->setDefaults($defaults);
        }

        if ($label !== null) {
            $this->content = $label;
        }

        if ($class) {
            $this->addClass($class);
        }
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
     * @return \atk4\data\Model
     */
    public function setModel(\atk4\data\Model $m)
    {
        $this->model = $m;

        return $m;
    }

    /**
     * Sets source of the View.
     *
     * @param array $data
     *
     * @return \atk4\data\Model
     */
    public function setSource(array $data)
    {
        $goodData = [];

        foreach ($data as $key => $value) {
            if (!is_array($value)) {
                $value = ['name' => $value];
            }

            if (!isset($value['id'])) {
                $value['id'] = $key;
            }
            $goodData[] = $value;
        }
        $goodData = ['data' => $goodData];

        $model = new \atk4\data\Model(
            new \atk4\data\Persistence_Array($goodData), 'data'
        );
        $model->addField('name');

        return $this->setModel($model);
    }

    /**
     * TODO: move into trait because it's used so often.
     *
     * @param $key
     * @param $val
     *
     * @throws Exception
     */
    protected function setMissingProperty($key, $val)
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
            'Unable to set property for the object',
            'object'   => $this,
            'property' => $key,
            'value'    => $val,
        ]);
    }

    /**
     * Sets View element.
     *
     * @param string $element
     *
     * @return $this
     */
    public function setElement($element)
    {
        $this->element = $element;

        return $this;
    }

    /**
     * Makes view into a "<a>" element with a link.
     *
     * @param string $url
     *
     * @return $this
     */
    public function link($url)
    {
        $this->element = 'a';
        if (is_string($url)) {
            $this->setAttr('href', $url);

            return $this;
        }
        $this->setAttr('href', $this->app->url($url));

        return $this;
    }

    // }}}

    // {{{ Default init() method and add() logic

    /**
     * Called when view becomes part of render tree. You can override it but avoid
     * placing any "heavy processing" here.
     */
    public function init()
    {
        // set name and id of view
        if (!$this->name) {
            if (!$this->id) {
                $this->id = $this->name = 'atk';
            } else {
                $this->name = $this->id;
            }
        } elseif (!$this->id) {
            $this->id = $this->name;
        }

        // initialize
        $this->_init();

        if (!$this->app) {
            $this->initDefaultApp();
        }

        if ($this->region && !$this->template && !$this->defaultTemplate && $this->owner && $this->owner->template) {
            $this->template = $this->owner->template->cloneRegion($this->region);

            $this->owner->template->del($this->region);
        } else {
            // set up template
            if (is_string($this->defaultTemplate) && is_null($this->template)) {
                $this->template = $this->app->loadTemplate($this->defaultTemplate);
            }

            if (!$this->region) {
                $this->region = 'Content';
            }
        }


        // add default objects
        foreach ($this->_add_later as list($object, $region)) {
            $this->add($object, $region);
        }

        $this->_add_later = [];
    }

    /**
     * For the absence of the application, we would add a very
     * simple one.
     */
    protected function initDefaultApp()
    {
        $this->app = new App(['skin' => $this->skin, 'catch_exceptions' => false, 'always_run' => false]);
        $this->app->init();
    }

    /**
     * In addition to adding a child object, set up it's template
     * and associate it's output with the region in our template.
     *
     * @param View|string  $object New object to add
     * @param string|array $region (or array for full set of defaults)
     *
     * @throws Exception
     *
     * @return View
     */
    public function add($seed, $region = null)
    {
        if (!$this->app) {
            $this->_add_later[] = [$seed, $region];

            return $seed;
        }

        if (is_array($region)) {
            throw new Exception('Second argument to add must be region or null!');
        }

        /*
        if (is_string($defaults)) {
            $defaults = ['region'=>$defaults];
        }
         */

        /*
        $region = null;
        if (is_array($defaults) && isset($defaults['region'])) {
            $region = $defaults['region'];
            unset($defaults['region']);
        } elseif (is_array($defaults) && isset($defaults[0])) {
            $region = $defaults[0];
            unset($defaults[0]);
        } elseif (is_string($defaults)) {
            $region = $defaults;
            $defaults = null;
        }
         */

        // Create object first
        $object = $this->factory($this->mergeSeeds($seed, ['View']), $region ? ['region'=>$region]: null);

        // Will call init() of the object
        $object = $this->_add($object);

        if (!$object instanceof self) {
            return $object;
        }

        // We are adding a new view, so do a bit more
        /*
        if (!$object->template && $object->region && $this->template) {
            $object->template = $this->template->cloneRegion($object->region);
        }

        if ($this->template && $object->region) {
            if (is_string($this->template)) {
                throw new Exception(['Property $template should contain object, not a string', 'template' => $this->template]);
            }

            $this->template->del($object->region);
        }
         */

        return $object;
    }

    // }}}

    // {{{ Manipulating classes and view properties

    /**
     * Override this method without compatibility with parrent, if you wish
     * to set your own things your own way for your view.
     *
     * @param string|array $arg1
     * @param string|null  $arg2
     *
     * @throws Exception
     *
     * @return $this
     */
    public function set($arg1 = null, $arg2 = null)
    {
        if (is_string($arg1) && $arg2 !== null) {

            // must be initialized
            $this->template->set($arg1, $arg2);

            return $this;
        }

        if ($arg2 !== null) {
            throw new Exception([
                'Second argument to set() can be only passed if the first one is a string',
                'arg1' => $arg1,
                'arg2' => $arg2,
            ]);
        }

        if (is_string($arg1)) {
            $this->content = $arg1;

            return $this;
        }

        if (is_array($arg1)) {
            if (isset($arg1[0])) {
                $this->content = $arg1[0];
            }
            $this->setDefaults($arg1);

            return $this;
        }

        throw new Exception([
            'Not sure what to do with argument',
            'this' => $this,
            'arg1' => $arg1,
            'arg2' => $arg2,
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

        if (!$this->class) {
            $this->class = [];
        }

        if (is_string($this->class)) {
            throw new Exception(['Property $class should always be array', 'object' => $this, 'class' => $this->class]);
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

    /**
     * Add inline CSS style to element.
     * Multiple CSS styles can also be set if passed as array.
     *
     * @param string|array $property CSS Property or hash
     * @param string       $style    CSS Style definition
     *
     * @return $this
     *
     * @todo Think about difference between setStyle and addStyle
     */
    public function setStyle($property, $style = null)
    {
        if (is_array($property) && $style === null) {
            foreach ($property as $k => $v) {
                $this->addStyle($k, $v);
            }

            return $this;
        }
        $this->style[$property] = $style;

        return $this;
    }

    /**
     * @see setStyle()
     */
    public function addStyle($property, $style = null)
    {
        return $this->setStyle($property, $style);
    }

    /**
     * Remove inline CSS style from element, if it was added with setStyle
     * or addStyle.
     *
     * @param string $property CSS Property to remove
     *
     * @return $this
     */
    public function removeStyle($property)
    {
        unset($this->style[$property]);

        return $this;
    }

    /**
     * Set attribute.
     *
     * @param string|array $attr  Attribute name or hash
     * @param string       $value Attribute value
     *
     * @return $this
     */
    public function setAttr($attr, $value = null)
    {
        if (is_array($attr)) {
            $this->attr = array_merge($this->attr, $attr);

            return $this;
        }

        $this->attr[$attr] = $value;

        return $this;
    }

    /**
     * Remove attribute.
     *
     * @param string|array $attr Attribute name or hash
     *
     * @return $this
     */
    public function removeAttr($property)
    {
        if (is_array($property)) {
            foreach ($property as $v) {
                unset($this->attr[$v]);
            }

            return $this;
        }

        unset($this->attr[$property]);

        return $this;
    }

    // }}}

    // {{{ Rendering

    /**
     * View-specific rendering stuff. Feel free to replace this method with
     * your own. View::renderView contains some logic that integrates with
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

        if ($this->style) {
            $style = $this->style;
            array_walk(
                $style,
                function (&$item, $key) {
                    $item = $key.':'.$item;
                }
            );
            $this->template->append('style', implode(';', $style));
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

        if ($this->attr) {
            $tmp = [];
            foreach ($this->attr as $attr => $val) {
                $tmp[] = $attr.'="'.$this->app->encodeAttribute($val).'"';
            }
            $this->template->setHTML('attributes', implode(' ', $tmp));
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

        if (isset($this->content) && $this->content !== false) {
            $this->template->append('Content', $this->content);
        }
    }

    /**
     * Render everything recursively, render ourselves but don't return
     * anything just yet.
     */
    public function renderAll()
    {
        if (!$this->_initialized) {
            $this->init();
        }

        if (!$this->_rendered) {
            $this->renderView();

            $this->recursiveRender();
            $this->_rendered = true;
        }
    }

    /**
     * This method is for those cases when developer want to simply render his
     * view and grab HTML himself.
     *
     * @param bool $force_echo
     *
     * @return string
     */
    public function render($force_echo = true)
    {
        $this->renderAll();

        return
            $this->getJS($force_echo).
            $this->template->render();
    }

    /**
     * Render View using json format.
     *
     * @param bool $force_echo
     *
     * @return string
     */
    public function renderJSON($force_echo = true)
    {
        try {
            $this->renderAll();

            return json_encode(['success' => true,
                                'message' => 'Success',
                                'atkjs'   => $this->getJS($force_echo),
                                'html'    => $this->template->render(),
                                'id'      => $this->name, ]);
        } catch (\Exception $exception) {
            $l = $this->add(new self());
            if ($exception instanceof \atk4\core\Exception) {
                $l->template->setHTML('Content', $exception->getHTML());
            } elseif ($exception instanceof \Error) {
                $l->add(new self(['ui' => 'message', get_class($exception).': '.
                                                            $exception->getMessage().' (in '.$exception->getFile().':'.$exception->getLine().')',
                    'error', ]));
                $l->add(new Text())->set(nl2br($exception->getTraceAsString()));
            } else {
                $l->add(new self(['ui' => 'message', get_class($exception).': '.$exception->getMessage(), 'error']));
            }

            return json_encode(['success' => false,
                                'message' => $l->getHTML(), ]);
        }
    }

    /**
     * Created for recursive rendering or when you want to only get HTML of
     * this object (not javascript).
     *
     * @return string
     */
    public function getHTML()
    {
        $this->renderAll();

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
     * @param string|bool|null $when   Event when chain will be executed
     * @param jsExpression     $action JavaScript action
     *
     * @return jQuery
     */
    public function js($when = null, $action = null, $selector = null)
    {
        if ($selector) {
            $chain = new jQuery($selector);
        } else {
            $chain = new jQuery($this);
        }

        // Substitute $when to make it better work as a array key
        if ($when === true) {
            $this->_js_actions[$when][] = $chain;

            if ($action) {
                $this->_js_actions[$when][] = $action;
            }

            return $chain;
        }

        if ($when === false || $when === null) {
            return $chain;
        }

        // next - binding on a specific event
        $action = (new jQuery($this))
            ->bind($when, new jsFunction([$chain, $action]));

        $this->_js_actions[$when][] = $action;

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
     * @param array            $defaults Options
     *
     * @throws Exception
     *
     * @return jQuery
     */
    public function on($event, $selector = null, $action = null, $defaults = null)
    {
        // second argument may be omitted
        if (!is_string($selector) && (is_null($action) || is_array($action))) {
            $defaults = $action;
            $action = $selector;
            $selector = null;
        }

        $arguments = isset($defaults['args']) ? $defaults['args'] : [];
        if (is_null($defaults)) {
            $defaults = [];
        }

        // all non-key items of defaults are actually arguments
        foreach ($defaults as $key => $value) {
            if (is_numeric($key)) {
                $arguments[] = $value;
                unset($defaults[$key]);
            }
        }

        $actions = [];
        $actions['preventDefault'] = true;
        $actions['stopPropagation'] = true;
        if (isset($defaults['preventDefault'])) {
            $actions['preventDefault'] = $defaults['preventDefault'];
        }
        if (isset($defaults['stopPropagation'])) {
            $actions['stopPropagation'] = $defaults['stopPropagation'];
        }

        if (is_callable($action) || (is_array($action) && isset($action[0]) && is_callable($action[0]))) {
            // if callable $action is passed, then execute ajaxec()

            if (is_array($action)) {
                $urlData = $action;
                unset($urlData[0]);
                $action = $action[0];
            } else {
                $urlData = [];
            }

            // create callback, that will include event as part of the full name
            $this->_add($cb = new jsCallback(), ['desired_name' => $event]);

            $cb->set(function () use ($action) {
                $args = func_get_args();
                $args[0] = new jQuery(new jsExpression('this'));

                return call_user_func_array($action, $args);
            }, $arguments);

            if (isset($defaults['confirm'])) {
                $cb->setConfirm($defaults['confirm']);
            }

            $actions[] = $cb;
            //$thisAction->api(['on'=>'now', 'url'=>$cb->getURL(), 'urlData'=>$urlData, 'obj'=>new jsExpression('this')]);
        } elseif ($action) {
            // otherwise include
            $actions[] = $action;
        }

        $chain = new jQuery();
        $actions[] = $chain;

        $action = new jsFunction($actions);

        if ($selector) {
            $this->js(true)->on($event, $selector, $action);
        } else {
            $this->js(true)->on($event, $action);
        }

        return $chain;
    }

    /**
     * Convert View into a value in case it happens to be inside our json_encode (as argument to jsChain).
     *
     * @throws Exception
     *
     * @return string
     */
    public function jsRender()
    {
        if (!$this->_initialized) {
            throw new Exception('Render tree must be initialized before materializing jsChains.');
        }

        return json_encode('#'.$this->id);
    }

    /**
     * Get JavaScript objects from this render tree.
     *
     * @param bool $force_echo
     *
     * @return string
     */
    public function getJS($force_echo = false)
    {
        $actions = [];

        foreach ($this->_js_actions as $eventActions) {
            foreach ($eventActions as $action) {
                $actions[] = $action;
            }
        }

        if (!$actions) {
            return '';
        }

        $actions['indent'] = '';

        if (!$force_echo && $this->app && $this->app->hasMethod('jsReady')) {
            $this->app->jsReady($actions);

            return '';
        }

        // delegate $action rendering in hosting app if exist.
        if ($this->app && $this->app->hasMethod('getViewJS')) {
            return $this->app->getViewJS($actions);
        }

        $ready = new jsFunction($actions);

        return "<script>\n".
            (new jQuery($ready))->jsRender().
            '</script>';
    }

    // }}}
}
