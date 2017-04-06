<?php

namespace atk4\ui;

class App
{
    use \atk4\core\InitializerTrait {
        init as _init;
    }

    use \atk4\core\HookTrait;

    // @var string Name of application
    public $title = 'Agile UI - Untitled Application';

    public $layout = null; // the top-most view object

    public $template_dir = null;

    // @var string Name of skin
    public $skin = 'semantic-ui';

    /**
     * Will replace an exception handler with our own, that will output errors nicely.
     */
    public $catch_exceptions = true;

    /**
     * Will always run application even if developer didn't explicitly executed run();.
     */
    public $always_run = true;

    public $run_called = false;

    public $is_rendering = false;

    public $ui_persistence = null;

    /**
     * If you specify a string, then it will be considered a filename
     * from which to load the template.
     *
     * @var string
     */
    public $defaultTemplate = 'html.html';

    /**
     * Constructor.
     *
     * @param array $defaults
     */
    public function __construct($defaults = [])
    {
        // Process defaults
        if (is_string($defaults)) {
            $defaults = ['title' => $defaults];
        }

        if (!is_array($defaults)) {
            throw new Exception(['Constructor requires array argument', 'arg' => $defaults]);
        }
        foreach ($defaults as $key => $val) {
            if (is_array($val)) {
                $this->$key = array_merge(isset($this->$key) && is_array($this->$key) ? $this->$key : [], $val);
            } elseif (!is_null($val)) {
                $this->$key = $val;
            }
        }

        // Set up template folder
        $this->template_dir = dirname(dirname(__FILE__)).'/template/'.$this->skin;

        // Set our exception handler
        if ($this->catch_exceptions) {
            set_exception_handler(function ($exception) {
                return $this->caughtException($exception);
            });
        }

        if (!$this->_initialized) {
            //$this->init();
        }

        // Always run app on shutdown
        if ($this->always_run) {
            register_shutdown_function(function () {
                if (!$this->run_called) {
                    try {
                        $this->run();
                    } catch (\Exception $e) {
                        $this->caughtException($e);
                    }
                }
                exit;
            });
        }

        // Set up UI persistence
        if (!isset($this->ui_persistence)) {
            $this->ui_persistence = new Persistence\UI();
        }
    }

    /**
     * Catch exception.
     *
     * @param mixed $exception
     */
    public function caughtException($exception)
    {
        $l = new \atk4\ui\App();
        $l->initLayout('Centered');
        if ($exception instanceof \atk4\core\Exception) {
            $l->layout->template->setHTML('Content', $exception->getHTML());
        } elseif ($exception instanceof \Error) {
            $l->layout->add(new View(['ui'=> 'message', get_class($exception).': '.
                $exception->getMessage().' (in '.$exception->getFile().':'.$exception->getLine().')',
                'error', ]));
            $l->layout->add(new Text())->set(nl2br($exception->getTraceAsString()));
        } else {
            $l->layout->add(new View(['ui'=>'message', get_class($exception).': '.$exception->getMessage(), 'error']));
        }
        $l->layout->template->tryDel('Header');
        $l->run();
        $this->run_called = true;
    }

    /**
     * Outputs debug info.
     *
     * @param string $str
     */
    public function outputDebug($str)
    {
        echo 'DEBUG:'.$str.'<br/>';
    }

    /**
     * Will perform a preemptive output and terminate. Do not use this
     * directly, instead call it form Callback, jsCallback or similar
     * other classes.
     *
     * @param string $output
     */
    public function terminate($output = null)
    {
        echo $output;
        $this->run_called = true; // prevent shutdown function from triggering.
        exit;
    }

    /**
     * Initializes layout.
     *
     * @param string|Layout\Generic $layout
     * @param array                 $options
     *
     * @return $this
     */
    public function initLayout($layout, $options = [])
    {
        if (is_string($layout)) {
            $layout = $this->normalizeClassNameApp($layout, 'Layout');
            $layout = new $layout($options);
        }
        $layout->app = $this;

        $this->html = new View(['defaultTemplate' => $this->defaultTemplate]);
        $this->html->app = $this;
        $this->html->init();
        $this->layout = $this->html->add($layout);

        return $this;
    }

    /**
     * Adds a <style> block to the HTML Header. Not escaped. Try to avoid
     * and use file include instead.
     *
     * @param string $style CSS rules, like ".foo { background: red }".
     */
    public function addStyle($style)
    {
        if (!$this->html) {
            throw new Exception(['App does not know how to add style']);
        }
        $this->html->template->appendHTML('HEAD', $this->getTag('style', $style));
    }

    /**
     * Normalizes class name.
     *
     * @param string $name
     * @param string $prefix
     *
     * @return string
     */
    public function normalizeClassNameApp($name, $prefix = null)
    {
        if (strpos('/', $name) === false && strpos('\\', $name) === false) {
            $name = '\\'.__NAMESPACE__.'\\'.($prefix ? ($prefix.'\\') : '').$name;
        }

        return $name;
    }

    /**
     * Create object and associate it with this app.
     *
     * @return object
     */
    public function add()
    {
        if ($this->layout) {
            return call_user_func_array([$this->layout, 'add'], func_get_args());
        } else {
            list($obj) = func_get_args();

            if (!is_object($obj)) {
                throw new Exception(['Incorrect use of App::add']);
            }

            $obj->app = $this;

            return $obj;
        }
    }

    /**
     * Runs app and echo rendered template.
     */
    public function run()
    {
        $this->run_called = true;
        $this->hook('beforeRender');
        $this->is_rendering = true;
        $this->html->template->set('title', $this->title);
        $this->html->renderAll();
        $this->html->template->appendHTML('HEAD', $this->html->getJS());
        $this->is_rendering = false;
        $this->hook('beforeOutput');
        echo $this->html->template->render();
    }

    /**
     * Initialize app.
     */
    public function init()
    {
        $this->_init();
    }

    /**
     * Load template.
     *
     * @param string $name
     *
     * @return Template
     */
    public function loadTemplate($name)
    {
        $template = new Template();
        $template->app = $this;
        if (in_array($name[0], ['.', '/', '\\'])) {
            $template->load($name);
        } else {
            $template->load($this->template_dir.'/'.$name);
        }

        return $template;
    }

    /**
     * Build a URL that application can use for call-backs.
     *
     * @param array|string $args List of new GET arguments
     *
     * @return string
     */
    public function url($args = [])
    {
        if (is_string($args)) {
            $args = [$args];
        }

        if (!isset($args[0])) {
            $args[0] = '';
        }

        $page = $args[0];
        unset($args[0]);

        $url = $page ? ($page.'.php') : '';

        $args = http_build_query($args);

        if ($args) {
            $url = $url.'?'.$args;
        }

        return $url;
    }

    /**
     * Adds additional JS include in aaplication template.
     *
     * @param string $name
     *
     * @return $this
     */
    public function requireJS($name)
    {
        $this->template->trySet('', $this->url($name));

        return $this;
    }


    /**
     * Construct HTML tag with supplied attributes.
     *
     * $html = getTag('img/', ['src'=>'foo.gif','border'=>0]);
     * // "<img src="foo.gif" border="0"/>"
     *
     *
     * The following rules are respected:
     *
     * 1. all array key=>val elements appear as attributes with value escaped.
     * getTag('div/', ['data'=>'he"llo']);
     * --> <div data="he\"llo">
     *
     * 2. boolean value true will add attribute without value
     * getTag('td', ['nowrap'=>true]);
     * --> <td nowrap>
     *
     * 3. null and false value will ignore the attribute
     * getTag('img', ['src'=>false]);
     * --> <img>
     *
     * 4. passing key 0=>"val" will re-define the element itself
     * getTag('img', ['input', 'type'=>'picture']);
     * --> <input type="picture" src="foo.gif">
     *
     * 5. use '/' at end of tag to close it.
     * getTag('img/', ['src'=>'foo.gif']);
     * --> <img src="foo.gif"/>
     *
     * 6. if main tag is self-closing, overriding it keeps it self-closing
     * getTag('img/', ['input', 'type'=>'picture']);
     * --> <input type="picture" src="foo.gif"/>
     *
     * 7. simple way to close tag. Any attributes to closing tags are ignored
     * getTag('/td');
     * --> </td>
     *
     * 7b. except for 0=>'newtag'
     * getTag('/td', ['th', 'align'=>'left']);
     * --> </th>
     *
     * 8. using $value will NOT be escaped (so that you can pass nested HTML)
     * getTag('a', ['href'=>'foo.html'] ,'click here >>');
     * --> <a href="foo.html">click here >></a>
     *
     * 9. you may skip attribute argument.
     * getTag('b','text in bold');
     * --> <b>text in bold</b>
     *
     * 10. pass array as text to net tags (array must contain 1 to 3 elements corresponding to arguments):
     * getTag('a', ['href'=>'foo.html'], ['b','click here']);
     * --> <a href="foo.html"><b>click here</b></a>
     *
     * @param string|array $tag
     * @param string       $attr
     * @param string|array $value
     *
     * @return string
     */
    public function getTag($tag = null, $attr = null, $value = null)
    {
        if ($tag === null) {
            $tag = 'div';
        } elseif (is_array($tag)) {
            $tmp = $tag;

            $tag = 'div';
            $value = '';

            if (isset($tmp[0])) {
                $tag = $tmp[0];
                unset($tmp[0]);
            }

            if (isset($tmp[1])) {
                $value = $tmp[1];
                unset($tmp[1]);
            }

            $attr = $tmp;
        }
        if ($tag[0] === '<') {
            return $tag;
        }
        if (is_string($attr)) {
            $value = $attr;
            $attr = null;
        }

        if (is_string($value)) {
            $value = $this->encodeHTML($value);
        } elseif (is_array($value)) {
            $value = $this->getTag($value);
        }

        if (!$attr) {
            return "<$tag>".($value ? ($value)."</$tag>" : '');
        }
        $tmp = [];
        if (substr($tag, -1) == '/') {
            $tag = substr($tag, 0, -1);
            $postfix = '/';
        } elseif (substr($tag, 0, 1) == '/') {
            if (isset($attr[0])) {
                return '</'.$attr[0].'>';
            }

            return '<'.$tag.'>';
        } else {
            $postfix = '';
        }
        foreach ($attr as $key => $val) {
            if ($val === false) {
                continue;
            }
            if ($val === true) {
                $tmp[] = "$key";
            } elseif ($key === 0) {
                $tag = $val;
            } else {
                $tmp[] = "$key=\"".$this->encodeAttribute($val).'"';
            }
        }

        return "<$tag".($tmp ? (' '.implode(' ', $tmp)) : '').$postfix.'>'.($value ? $value."</$tag>" : '');
    }

    /**
     * Encodes string - removes HTML special chars.
     *
     * @param string $val
     *
     * @return string
     */
    public function encodeAttribute($val)
    {
        return htmlspecialchars($val);
    }

    /**
     * Encodes string - removes HTML entities.
     *
     * @param string $val
     *
     * @return string
     */
    public function encodeHTML($val)
    {
        return htmlentities($val);
    }
}
