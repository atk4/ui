<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Data\Model;
use Atk4\Data\Persistence;
use Atk4\Ui\Js\Jquery;
use Atk4\Ui\Js\JsBlock;
use Atk4\Ui\Js\JsChain;
use Atk4\Ui\Js\JsExpression;
use Atk4\Ui\Js\JsExpressionable;
use Atk4\Ui\Js\JsFunction;
use Atk4\Ui\Js\JsReload;
use Atk4\Ui\Js\JsVueService;
use Atk4\Ui\UserAction\ExecutorFactory;

/**
 * Base view of all UI components.
 *
 * @phpstan-type JsCallbackSetClosure \Closure(Jquery, mixed, mixed, mixed, mixed, mixed, mixed, mixed, mixed, mixed, mixed): (JsExpressionable|View|string|void)
 */
class View extends AbstractView
{
    /**
     * When you call render() this will be populated with JavaScript chains.
     *
     * @var array<1|string, array<int, JsExpressionable>>
     *
     * @internal
     */
    protected array $_jsActions = [];

    public ?Model $model = null;

    /**
     * Name of the region in the parent's template where this object will output itself.
     */
    public ?string $region = null;

    /**
     * Enables UI keyword for Fomantic-UI indicating that this is a
     * UI element. If you set this variable value to string, it will
     * be appended at the end of the element class.
     *
     * @var bool|string
     */
    public $ui = false;

    /** @var array<int, string> List of element CSS classes. */
    public array $class = [];

    /** @var array<string, string> Map of element CSS styles. */
    public array $style = [];

    /** @var array<string, string|int> Map of element attributes. */
    public array $attr = [];

    /**
     * Template object, that, for most Views will be rendered to
     * produce HTML output. If you leave this object as "null" then
     * a new Template will be generated during init() based on the
     * value of $defaultTemplate.
     *
     * @var HtmlTemplate|null
     */
    public $template;

    /**
     * Specifies how to initialize $template.
     *
     * If you specify a string, then it will be considered a filename
     * from which to load the $template.
     *
     * @var string|null
     */
    public $defaultTemplate = 'element.html';

    /** @var string|null Set static contents of this view. */
    public $content;

    /** Change this if you want to substitute default "div" for something else. */
    public string $element = 'div';

    /** @var ExecutorFactory|null */
    protected $executorFactory;

    // {{{ Setting Things up

    /**
     * @param array<0|string, mixed>|string $label
     */
    public function __construct($label = [])
    {
        if ('func_num_args'() > 1) { // prevent bad usage
            throw new \Error('Too many method arguments');
        }

        $defaults = is_array($label) ? $label : [$label];

        if (array_key_exists(0, $defaults)) {
            $defaults['content'] = $defaults[0];
            unset($defaults[0]);
        }

        $this->setDefaults($defaults);
    }

    /**
     * Associate this view with a model. Do not place any logic in this class, instead take it
     * to renderView().
     *
     * Do not try to create your own "Model" implementation, instead you must be looking for
     * your own "Persistence" implementation.
     *
     * @phpstan-assert !null $this->model
     */
    public function setModel(Model $model): void
    {
        if ($this->model !== null && $this->model !== $model) {
            throw new Exception('Different model is already set');
        }

        $this->model = $model;
    }

    /**
     * Sets source of the View.
     *
     * @param array $fields Limit model to particular fields
     *
     * @phpstan-assert !null $this->model
     */
    public function setSource(array $data, $fields = null): Model
    {
        // ID with zero value is not supported (at least in MySQL replaces it with next AI value)
        if (isset($data[0])) {
            if (array_is_list($data)) {
                $oldData = $data;
                $data = [];
                foreach ($oldData as $k => $row) {
                    $data[$k + 1_000_000_000] = $row; // large offset to prevent accessing wrong data by old key
                }
            } else {
                throw new Exception('Source data contains unsupported zero key');
            }
        }

        $this->setModel(new Model(new Persistence\Static_($data)), $fields); // @phpstan-ignore-line
        $this->model->getField($this->model->idField)->type = 'string'; // TODO probably unwanted

        return $this->model;
    }

    /**
     * @param mixed $value
     */
    protected function setMissingProperty(string $propertyName, $value): void
    {
        if (is_bool($value) && str_starts_with($propertyName, 'class.')) {
            $class = substr($propertyName, strlen('class.'));
            if ($value) {
                $this->addClass($class);
            } else {
                $this->removeClass($class);
            }

            return;
        }

        parent::setMissingProperty($propertyName, $value);
    }

    /**
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
     * @param string|array<0|string, string|int|false> $url
     *
     * @return $this
     */
    public function link($url, string $target = null)
    {
        $this->setElement('a');

        if (is_string($url)) {
            $this->setAttr('href', $url);
        } else {
            $this->setAttr('href', $this->url($url));
        }

        if ($target !== null) {
            $this->setAttr('target', $target);
        }

        return $this;
    }

    // }}}

    // {{{ Default init() method and add() logic

    /**
     * Called when view becomes part of render tree. You can override it but avoid
     * placing any "heavy processing" here.
     */
    protected function init(): void
    {
        // almost every View needs an App to load a template, so assert App is set upfront
        // TODO consider lazy loading the template
        $app = $this->getApp();

        $addLater = $this->_addLater;
        $this->_addLater = null;

        parent::init();

        if ($this->region === null) {
            $this->region = 'Content';
        }

        if ($this->template === null) {
            if ($this->defaultTemplate !== null) {
                $this->template = $app->loadTemplate($this->defaultTemplate);
            } else {
                if ($this->region !== 'Content' && $this->issetOwner() && $this->getOwner()->template) {
                    $this->template = $this->getOwner()->template->cloneRegion($this->region);
                    $this->getOwner()->template->del($this->region);
                }
            }
        }

        if ($this->template !== null && (!$this->template->issetApp() || $this->template->getApp() !== $app)) {
            $this->template->setApp($app);
        }

        foreach ($addLater as [$object, $region]) {
            $this->add($object, $region);
        }

        // allow for injecting the model with a seed
        if ($this->model !== null) {
            $this->setModel($this->model);
        }
    }

    public function getExecutorFactory(): ExecutorFactory
    {
        return $this->executorFactory ?? $this->getApp()->getExecutorFactory();
    }

    /**
     * In addition to adding a child object, sets up it's template
     * and associate it's output with the region in our template.
     *
     * @param AbstractView      $object
     * @param string|array|null $region
     */
    public function add($object, $region = null): AbstractView
    {
        if ('func_num_args'() > 2) { // prevent bad usage
            throw new \Error('Too many method arguments');
        }

        if (!is_object($object)) { // @phpstan-ignore-line
            // for BC do not throw
            // later consider to accept strictly objects only
            $object = AbstractView::fromSeed($object);
        }

        if (!$this->issetApp()) {
            $this->_addLater[] = [$object, $region];

            return $object;
        }

        if (is_array($region)) {
            $args = $region;
            $region = $args['region'] ?? null;
            unset($args['region']);
        } else {
            $args = [];
        }

        // set region
        if ($region !== null) {
            $object->setDefaults(['region' => $region]);
        }

        // will call init() of the object
        parent::add($object, $args);

        return $object;
    }

    /**
     * Get closest owner which is instance of particular class.
     *
     * @template T of View
     *
     * @param class-string<T> $class
     *
     * @return T|null
     */
    public function getClosestOwner(string $class): ?self
    {
        if (!$this->issetOwner()) {
            return null;
        }

        if ($this->getOwner() instanceof $class) {
            return $this->getOwner();
        }

        return $this->getOwner()->getClosestOwner($class);
    }

    // }}}

    // {{{ Manipulating classes and view properties

    /**
     * TODO this method is hard to override, drop it from View.
     *
     * @param string $content
     *
     * @return $this
     */
    public function set($content)
    {
        if ('func_num_args'() > 1) { // prevent bad usage
            throw new Exception('Only one argument is needed by View::set()');
        }

        if (!is_string($content) && $content !== null) { // @phpstan-ignore-line
            throw (new Exception('Not sure what to do with argument'))
                ->addMoreInfo('this', $this)
                ->addMoreInfo('arg', $content);
        }

        $this->content = $content;

        return $this;
    }

    /**
     * Add CSS class to element. Previously added classes are not affected.
     * Multiple CSS classes can also be added if passed as space separated
     * string or array of class names.
     *
     * @param string|array<int, string> $class
     *
     * @return $this
     */
    public function addClass($class)
    {
        if ($class !== []) {
            $classArr = explode(' ', is_array($class) ? implode(' ', $class) : $class);
            $this->class = array_merge($this->class, $classArr);
        }

        return $this;
    }

    /**
     * Remove one or several CSS classes from the element.
     *
     * @param string|array<int, string> $class
     *
     * @return $this
     */
    public function removeClass($class)
    {
        $classArr = explode(' ', is_array($class) ? implode(' ', $class) : $class);
        $this->class = array_diff($this->class, $classArr);

        return $this;
    }

    /**
     * Add inline CSS style to element.
     * Multiple CSS styles can also be set if passed as array.
     *
     * @param string|array<string, string>          $property
     * @param ($property is array ? never : string) $value
     *
     * @return $this
     */
    public function setStyle($property, string $value = null)
    {
        if (is_array($property)) {
            foreach ($property as $k => $v) {
                $this->setStyle($k, $v);
            }
        } else {
            $this->style[$property] = $value;
        }

        return $this;
    }

    /**
     * Remove inline CSS style from element.
     *
     * @param string $property
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
     * @param string|int|array<string, string|int>  $name
     * @param ($name is array ? never : string|int) $value
     *
     * @return $this
     */
    public function setAttr($name, $value = null)
    {
        if (is_array($name)) {
            foreach ($name as $k => $v) {
                $this->setAttr($k, $v);
            }
        } else {
            $this->attr[$name] = $value;
        }

        return $this;
    }

    /**
     * Remove attribute.
     *
     * @param string|array<int, string> $name
     *
     * @return $this
     */
    public function removeAttr($name)
    {
        if (is_array($name)) {
            foreach ($name as $v) {
                $this->removeAttr($v);
            }
        } else {
            unset($this->attr[$name]);
        }

        return $this;
    }

    // }}}

    // {{{ Sticky URLs

    /** @var array<string, string> stickyGet arguments */
    public $stickyArgs = [];

    /**
     * Build an URL which this view can use for callbacks.
     *
     * @param string|array<0|string, string|int|false> $page URL as string or array with page path as first element and other GET arguments
     */
    public function url($page = []): string
    {
        return $this->getApp()->url($page, $this->_getStickyArgs());
    }

    /**
     * Build an URL which this view can use for JS callbacks.
     *
     * @param string|array<0|string, string|int|false> $page URL as string or array with page path as first element and other GET arguments
     */
    public function jsUrl($page = []): string
    {
        return $this->getApp()->jsUrl($page, $this->_getStickyArgs());
    }

    /**
     * Get sticky arguments defined by the view and parents (including API).
     */
    protected function _getStickyArgs(): array
    {
        if ($this->issetOwner()) {
            $stickyArgs = array_merge($this->getOwner()->_getStickyArgs(), $this->stickyArgs);
        } else {
            $stickyArgs = $this->stickyArgs;
        }

        return $stickyArgs;
    }

    /**
     * Mark GET argument as sticky. Calling url() on this view or any
     * sub-views will embed the value of this GET argument.
     *
     * If GET argument is empty or false, it won't make into URL.
     *
     * If GET argument is not presently set you can specify a 2nd argument
     * to forge-set the GET argument for current view and it's sub-views.
     */
    public function stickyGet(string $name, string $newValue = null): ?string
    {
        $this->stickyArgs[$name] = $newValue ?? $this->stickyArgs[$name] ?? $this->getApp()->tryGetRequestQueryParam($name);

        return $this->stickyArgs[$name];
    }

    // }}}

    // {{{ Rendering

    /**
     * View-specific rendering stuff. Feel free to replace this method with
     * your own. View::renderView contains some logic that integrates with
     * Fomantic-UI.
     */
    protected function renderView(): void
    {
        if ($this->element !== 'div') {
            $this->template->set('_element', $this->element);
        } else {
            $this->template->trySet('_element', $this->element);
        }

        $app = $this->getApp();
        if (!$app->isVoidTag($this->element)) {
            $this->template->tryDangerouslySetHtml('_element_end', '</' . $this->element . '>');
        }

        $attrsHtml = [];

        if ($this->name) {
            $attrsHtml[] = 'id="' . $app->encodeHtml($this->name) . '"';

            // TODO hack for template/tabs.html
            if ($this->template->hasTag('Tabs')) {
                array_pop($attrsHtml);
            }

            // TODO hack for template/form/control/upload.html
            if ($this->template->hasTag('AfterBeforeInput') && str_contains($this->template->renderToHtml(), ' type="file"')) {
                array_pop($attrsHtml);
            }

            // needed for templates like '<input id="{$_id}_input">'
            $this->template->trySet('_id', $this->name);
        }

        $class = null;
        if ($this->class !== []) {
            $class = implode(' ', $this->class);

            // needed for templates like template/form/layout/generic-input.html
            $this->template->tryAppend('class', implode(' ', $this->class));
        }
        if ($this->ui !== false) {
            $class = 'ui ' . $this->ui . ($class !== null ? ' ' . $class : '');
        }
        if ($class !== null) {
            $attrsHtml[] = 'class="' . $app->encodeHtml($class) . '"';
        }

        if ($this->style !== []) {
            $styles = [];
            foreach ($this->style as $k => $v) {
                $styles[] = $k . ': ' . $app->encodeHtml($v) . ';';
            }
            $attrsHtml[] = 'style="' . implode(' ', $styles) . '"';

            // needed for template/html.html
            $this->template->tryDangerouslyAppendHtml('style', implode(' ', $styles));
        }

        foreach ($this->attr as $k => $v) {
            $attrsHtml[] = $k . '="' . $app->encodeHtml((string) $v) . '"';
        }

        if ($attrsHtml !== []) {
            try {
                $this->template->dangerouslySetHtml('attributes', implode(' ', $attrsHtml));
            } catch (Exception $e) {
                // TODO hack to ignore missing '{$attributes}' mostly in layout templates
                if (count($attrsHtml) === 1 ? !str_starts_with(reset($attrsHtml), 'id=') : !$this instanceof Lister) {
                    throw $e;
                }
            }
        }
    }

    /**
     * Recursively render all children, placing their output in our template.
     */
    protected function recursiveRender(): void
    {
        foreach ($this->elements as $view) {
            if (!$view instanceof self) {
                continue;
            }

            $this->template->dangerouslyAppendHtml($view->region, $view->getHtml());

            // collect JS from everywhere
            foreach ($view->_jsActions as $when => $actions) {
                foreach ($actions as $action) {
                    $this->_jsActions[$when][] = $action;
                }
            }
        }

        if ($this->content !== null) {
            $this->template->append('Content', $this->content);
        }
    }

    /**
     * Render everything recursively, render ourselves but don't return anything just yet.
     */
    public function renderAll(): void
    {
        if (!$this->isInitialized()) {
            $this->invokeInit();
        }

        if (!$this->_rendered) {
            $this->renderView();

            $this->recursiveRender();
            $this->_rendered = true;
        }
    }

    /**
     * For Form::renderTemplateToHtml() only.
     */
    protected function renderTemplateToHtml(): string
    {
        return $this->template->renderToHtml();
    }

    /**
     * This method is for those cases when developer want to simply render his
     * view and grab HTML himself.
     */
    public function render(): string
    {
        $this->renderAll();

        $js = $this->getJs();

        return ($js !== '' ? $this->getApp()->getTag('script', [], '$(function () {' . $js . ';});') : '')
               . $this->renderTemplateToHtml();
    }

    /**
     * This method is to render view to place inside a Fomantic-UI Tab.
     */
    public function renderToTab(): array
    {
        $this->renderAll();

        return [
            'atkjs' => $this->getJsRenderActions(),
            'html' => $this->renderTemplateToHtml(),
        ];
    }

    /**
     * Render View using JSON format.
     */
    public function renderToJsonArr(): array
    {
        $this->renderAll();

        return [
            'success' => true,
            'atkjs' => $this->getJs(),
            'html' => $this->renderTemplateToHtml(),
            'id' => $this->name,
        ];
    }

    /**
     * Created for recursive rendering or when you want to only get HTML of
     * this object (not javascript).
     *
     * @return string
     */
    public function getHtml()
    {
        if ($this->getApp()->hasRequestQueryParam('__atk_reload') && $this->getApp()->getRequestQueryParam('__atk_reload') === $this->name) {
            $this->getApp()->terminateJson($this);
        }

        $this->renderAll();

        return $this->renderTemplateToHtml();
    }

    // }}}

    // {{{ JavaScript integration

    /**
     * Views in Agile UI can assign javascript actions to themselves. This
     * is done by calling $view->js() method which returns instance of JsChain
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
     * action = false will still return JsChain but will not bind it.
     * You can bind it by passing object into on() method.
     *
     * 1. Calling with arguments:
     * $view->js(); // technically does nothing
     * $a = $view->js()->hide(); // creates chain for hiding $view but does not bind to event yet
     *
     * 2. Binding existing chains
     * $img->on('mouseenter', $a); // binds previously defined chain to event on event of $img
     *
     * Produced code: $('#img_id').on('mouseenter', function (event) {
     *     event.preventDefault();
     *     event.stopPropagation();
     *     $('#view1').hide();
     * });
     *
     * 3. $button->on('click', $form->js()->submit()); // clicking button will result in form submit
     *
     * 4. $view->js(true)->find('.current')->text($text);
     *
     * Will convert calls to jQuery chain into JavaScript string:
     *  $('#view').find('.current').text('abc'); // the text will be JSON encoded to avoid JS injection
     *
     * @param bool|string                                     $when     Event when chain will be executed
     * @param ($when is false ? null : JsExpressionable|null) $action   JavaScript action
     * @param string|self|null                                $selector If you wish to override jQuery($selector)
     *
     * @return ($action is null ? Jquery : null)
     */
    public function js($when = false, $action = null, $selector = null)
    {
        // binding on a specific event
        // TODO allow only boolean $when, otherwise user should use self::on() method
        if (!is_bool($when)) {
            return $this->on($when, $selector, $action);
        }

        if ($action !== null) {
            $res = null;
        } else {
            $action = new Jquery($this);
            if ($selector) {
                $action->find($selector);
            }
            $res = $action;
        }

        if ($when === true) {
            $this->_jsActions[$when][] = $action;
        }

        return $res;
    }

    /**
     * Create Vue.js instance.
     * Vue.js instance can be created from Atk4\Ui\View.
     *
     * Component managed and defined by atk does not need componentDefinition variable name
     * because these are already loaded within the atk js namespace.
     * When creating your own component externally, you must supply the variable name holding
     * your Vue component definition. This definition must be also accessible within the window javascript
     * object. This way, you do not need to load Vue js file since it has already being include within
     * atkjs-ui.js build.
     *
     * If the external component use other components, it is possible to register them using
     * vueService getVue() method. This method return the current Vue object.
     * ex: atk.vueService.getVue().component('external_component', externalComponent). This is the same
     * as Vue.component() method.
     *
     * @param string                $component           The component name
     * @param array                 $initData            The component properties passed as the initData prop.
     *                                                   This is the initial data pass to your main component via the initData bind property
     *                                                   of the vue component instance created via the vueService.
     * @param JsExpressionable|null $componentDefinition component definition object
     * @param string|self|null      $selector            the selector for creating the base root object in Vue
     *
     * @return $this
     */
    public function vue($component, $initData = [], $componentDefinition = null, $selector = null)
    {
        if (!$selector) {
            $selector = '#' . $this->getHtmlId();
        }

        if ($componentDefinition) {
            $chain = (new JsVueService())->createVue($selector, $component, $componentDefinition, $initData);
        } else {
            $chain = (new JsVueService())->createAtkVue($selector, $component, $initData);
        }

        $this->js(true, $chain);

        return $this;
    }

    /**
     * Emit an event on atkEvent bus.
     *
     * example of adding a listener on for an emit event.
     *
     * atk.eventBus.on('eventName', (data) => {
     *     console.log(data)
     * });
     *
     * Note: In order to make sure your event is unique within atk, you can
     * use the view name in it.
     *    $this->jsEmitEvent($this->name . '-my-event', $data)
     */
    public function jsEmitEvent(string $eventName, array $eventData = []): JsChain
    {
        return (new JsChain('atk.eventBus'))->emit($eventName, $eventData);
    }

    /**
     * Get Local and Session web storage associated with this view.
     * Web storage can be retrieved using a $view->jsReload() request.
     */
    public function jsGetStoreData(): array
    {
        $data = [];
        $data['local'] = $this->getApp()->decodeJson(
            $this->getApp()->tryGetRequestQueryParam($this->name . '_local_store') ?? $this->getApp()->tryGetRequestPostParam($this->name . '_local_store') ?? 'null'
        );
        $data['session'] = $this->getApp()->decodeJson(
            $this->getApp()->tryGetRequestQueryParam($this->name . '_session_store') ?? $this->getApp()->tryGetRequestPostParam($this->name . '_session_store') ?? 'null'
        );

        return $data;
    }

    /**
     * Clear Web storage data associated with this view.
     */
    public function jsClearStoreData(bool $useSession = false): JsExpressionable
    {
        $type = $useSession ? 'session' : 'local';

        $name = $this->name;
        if (!$name) {
            throw new Exception('View property name needs to be set');
        }

        return (new JsChain('atk.dataService'))->clearData($name, $type);
    }

    /**
     * Add Web storage for this specific view.
     * Data will be store as json value where key name
     * will be the name of this view.
     *
     * Data added to web storage is merge against previous value.
     *  $v->jsAddStoreData(['args' => ['path' => '.']]);
     *  $v->jsAddStoreData(['args' => ['path' => '/'], 'fields' => ['name' => 'test']]]);
     *
     *  Final store value will be: ['args' => ['path' => '/'], 'fields' => ['name' => 'test']];
     */
    public function jsAddStoreData(array $data, bool $useSession = false): JsExpressionable
    {
        $type = $useSession ? 'session' : 'local';

        $name = $this->name;
        if (!$name) {
            throw new Exception('View property name needs to be set');
        }

        return (new JsChain('atk.dataService'))->addJsonData($name, $this->getApp()->encodeJson($data), $type);
    }

    /**
     * Returns JS for reloading View.
     *
     * @param array                 $args
     * @param JsExpressionable|null $afterSuccess
     * @param array                 $apiConfig
     *
     * @return JsReload
     */
    public function jsReload($args = [], $afterSuccess = null, $apiConfig = []): JsExpressionable
    {
        return new JsReload($this, $args, $afterSuccess, $apiConfig);
    }

    /**
     * Views in Agile Toolkit can assign javascript actions to themselves. This
     * is done by calling $view->js() or $view->on().
     *
     * on() method is similar to jQuery on(event, [selector, ] action) method.
     *
     * When no $action is passed, the on() method returns a chain corresponding to the affected element.
     *
     * Here are some ways to use on():
     *
     * // clicking on button will make the $view disappear
     * $button->on('click', $view->js()->hide());
     *
     * // clicking on <a class="clickable"> will make it's parent disappear
     * $view->on('click', 'a[data=clickable]')->parent()->hide();
     *
     * Finally, it's also possible to use PHP closure as an action:
     *
     * $view->on('click', 'a', function (Jquery $js, $data) {
     *   if (!$data['clickable']) {
     *      return new JsExpression('alert([])', ['This record is not clickable'])
     *   }
     *   return $js->parent()->hide();
     * });
     *
     * @param string                                                                                                                                                                                       $event    JavaScript event
     * @param ($action is object ? string : ($action is null ? string : never)|JsExpressionable|JsCallback|JsCallbackSetClosure|array{JsCallbackSetClosure}|UserAction\ExecutorInterface|Model\UserAction) $selector Optional jQuery-style selector
     * @param ($selector is string|null ? JsExpressionable|JsCallback|JsCallbackSetClosure|array{JsCallbackSetClosure}|UserAction\ExecutorInterface|Model\UserAction : array)                              $action   code to execute
     *
     * @return ($selector is string|null ? ($action is null ? Jquery : null) : ($action is array|null ? Jquery : null))
     */
    public function on(string $event, $selector = null, $action = null, array $defaults = [])
    {
        // second argument may be omitted
        if ($selector !== null && !is_string($selector) && ($action === null || is_array($action)) && $defaults === []) {
            $defaults = $action ?? [];
            $action = $selector;
            $selector = null;
        }

        // check for arguments
        $arguments = $defaults['args'] ?? [];
        unset($defaults['args']);

        // all values with int keys of defaults are arguments
        foreach ($defaults as $key => $value) {
            if (is_int($key)) {
                $arguments[] = $value;
                unset($defaults[$key]);
            }
        }

        if ($action !== null) {
            $res = null;
        } else {
            $action = new Jquery();
            $res = $action;
        }

        // set preventDefault and stopPropagation by default
        $eventStatements = [];
        $eventStatements['preventDefault'] = $defaults['preventDefault'] ?? true;
        $eventStatements['stopPropagation'] = $defaults['stopPropagation'] ?? true;

        $lazyJsRenderFx = function (\Closure $fx): JsExpressionable {
            return new class($fx) implements JsExpressionable {
                public \Closure $fx;

                /**
                 * @param \Closure(JsExpressionable): JsExpressionable $fx
                 */
                public function __construct(\Closure $fx)
                {
                    $this->fx = $fx;
                }

                public function jsRender(): string
                {
                    return ($this->fx)()->jsRender();
                }
            };
        };

        // dealing with callback action
        if ($action instanceof \Closure || (is_array($action) && ($action[0] ?? null) instanceof \Closure)) {
            $actions = [];
            if (is_array($action)) {
                $urlData = $action;
                unset($urlData[0]);
                foreach ($urlData as $a) {
                    $actions[] = $a;
                }
                $action = $action[0];
            }

            // create callback, that will include event as part of the full name
            $cb = JsCallback::addTo($this, [], [['desired_name' => $event]]);
            if ($defaults['apiConfig'] ?? null) {
                $cb->apiConfig = $defaults['apiConfig'];
            }

            $cb->set(static function (Jquery $chain, ...$args) use ($action) {
                return $action($chain, ...$args);
            }, $arguments);

            $actions[] = $lazyJsRenderFx(static fn () => $cb->jsExecute());
        } elseif ($action instanceof UserAction\ExecutorInterface || $action instanceof UserAction\SharedExecutor || $action instanceof Model\UserAction) {
            $ex = $action instanceof Model\UserAction ? $this->getExecutorFactory()->createExecutor($action, $this) : $action;

            $setupNonSharedExecutorFx = static function (UserAction\ExecutorInterface $ex) use (&$defaults, &$arguments): void {
                /** @var AbstractView&UserAction\ExecutorInterface $ex https://github.com/phpstan/phpstan/issues/3770 */
                $ex = $ex;

                if (isset($arguments['id'])) {
                    $arguments[$ex->name] = $arguments['id'];
                    unset($arguments['id']);
                } elseif (isset($arguments[0])) {
                    // if "id" is not specified we assume arguments[0] is the model ID
                    $arguments[$ex->name] = $arguments[0];
                    unset($arguments[0]);
                }

                if ($ex instanceof UserAction\JsCallbackExecutor) {
                    $confirmation = $ex->getAction()->getConfirmation();
                    if ($confirmation) {
                        $defaults['confirm'] = $confirmation;
                    }
                    if ($defaults['apiConfig'] ?? null) {
                        $ex->apiConfig = $defaults['apiConfig'];
                    }
                }
            };

            if ($ex instanceof UserAction\SharedExecutor) {
                $setupNonSharedExecutorFx($ex->getExecutor());
                $actions = [$ex->getExecutor() instanceof UserAction\JsCallbackExecutor
                    ? $lazyJsRenderFx(static fn () => $ex->jsExecute($arguments))
                    : $ex->jsExecute($arguments)];
            } elseif ($ex instanceof UserAction\JsExecutorInterface && $ex instanceof self) {
                $setupNonSharedExecutorFx($ex);
                $ex->executeModelAction();
                $actions = [$ex->jsExecute($arguments)];
            } elseif ($ex instanceof UserAction\JsCallbackExecutor) {
                $setupNonSharedExecutorFx($ex);
                $ex->executeModelAction();
                $actions = [$lazyJsRenderFx(static fn () => $ex->jsExecute($arguments))];
            } else {
                throw new Exception('Executor must be of type UserAction\JsCallbackExecutor or UserAction\JsExecutorInterface');
            }
        } elseif ($action instanceof JsCallback) {
            $actions = [$lazyJsRenderFx(static fn () => $action->jsExecute())];
        } else {
            $actions = [$action];
        }

        if ($defaults['confirm'] ?? null) {
            array_unshift($eventStatements, new JsExpression('$.atkConfirm({ message: [confirm], onApprove: [action], options: { button: { ok: [ok], cancel: [cancel] } }, context: this })', [
                'confirm' => $defaults['confirm'],
                'action' => new JsFunction([], $actions),
                'ok' => $defaults['ok'] ?? 'Ok',
                'cancel' => $defaults['cancel'] ?? 'Cancel',
            ]));
        } else {
            $eventStatements = array_merge($eventStatements, $actions);
        }

        $eventFunction = new JsFunction([], $eventStatements);
        $eventChain = new Jquery($this);
        if ($selector) {
            $eventChain->on($event, $selector, $eventFunction);
        } else {
            $eventChain->on($event, $eventFunction);
        }

        $this->_jsActions[$event][] = $eventChain;

        return $res;
    }

    public function getHtmlId(): string
    {
        $this->assertIsInitialized();

        return $this->name;
    }

    /**
     * Return rendered js actions as a string.
     */
    public function getJsRenderActions(): string
    {
        $actions = [];
        foreach ($this->_jsActions as $eventActions) {
            foreach ($eventActions as $action) {
                $actions[] = $action;
            }
        }

        return (new JsBlock($actions))->jsRender();
    }

    /**
     * Get JavaScript objects from this render tree.
     *
     * TODO dedup with getJsRenderActions()
     *
     * @return string
     */
    public function getJs()
    {
        $actions = [];
        foreach ($this->_jsActions as $eventActions) {
            foreach ($eventActions as $action) {
                $actions[] = $action;
            }
        }

        if (count($actions) === 0) {
            return '';
        }

        return (new JsExpression('[]()', [new JsFunction([], $actions)]))->jsRender();
    }

    // }}}
}
