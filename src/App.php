<?php

namespace atk4\ui;

class App
{
    use \atk4\core\InitializerTrait {
        init as _init;
    }

    public $title = 'Agile UI - Untitled Application';

    public $layout = null; // the top-most view object

    public $template_dir = null;

    public $skin = 'semantic-ui';

    /**
     * Will replace an exception handler with our own, that will output errors nicely.
     */
    public $catch_exceptions = true;

    /**
     * Will always run application even if developer didn't explicitly executed run();.
     */
    public $always_run = true;

    private $run_called = false;

    public function __construct($defaults = [])
    {
        if (is_string($defaults)) {
            $defaults = ['title'=>$defaults];
        }

        $this->template_dir = dirname(dirname(__FILE__)).'/template/'.$this->skin;

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

        // Set our exception handler
        if ($this->catch_exceptions) {
            set_exception_handler(function ($exception) {
                return $this->caughtException($exception);
            });
        }

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
    }

    public function caughtException(\Throwable $exception)
    {
        $l = new \atk4\ui\App();
        $l->initLayout('Centered');
        if ($exception instanceof \atk4\core\Exception) {
            $l->layout->template->setHTML('Content', $exception->getHTML());
        } elseif ($exception instanceof \Error) {
            $l->layout->add(new View(['ui'=> 'message', get_class($exception).': '.$exception->getMessage().' (in '.
                $exception->getFile().':'.$exception->getLine()
                .')', 'error', ]));
            $l->layout->add(new Text())->set(nl2br($exception->getTraceAsString()));
        } else {
            $l->layout->add(new View(['ui'=>'message', get_class($exception).': '.$exception->getMessage(), 'error']));
        }
        $l->layout->template->tryDel('Header');
        $l->run();
        $this->run_called = true;
    }

    public function initLayout($layout, $options = [])
    {
        if (is_string($layout)) {
            $layout = 'atk4\\ui\\Layout\\'.$layout;
            $layout = new $layout($options);
        }
        $layout->app = $this;

        $this->html = new View(['defaultTemplate'=>'html.html']);
        $this->html->app = $this;
        $this->html->init();
        $this->layout = $this->html->add($layout);

        return $this;
    }

    public function normalizeClassName($name, $prefix = null)
    {
        if (strpos('/', $name) === false && strpos('\\', $name) === false) {
            $name = 'atk4/ui/'.($prefix ? ($prefix.'/') : '').$name;
        }
        if ($name === 'HelloWorld') {
            return 'atk4/ui/HelloWorld';
        }

        return $name;
    }

    public function add()
    {
        return call_user_func_array([$this->layout, 'add'], func_get_args());
    }

    public function run()
    {
        $this->run_called = true;
        $this->html->template->set('title', $this->title);
        $this->html->renderAll();
        $this->html->template->appendHTML('HEAD', $this->html->getJS());
        echo $this->html->template->render();
    }

    public function init()
    {
        $this->_init();
    }

    public function loadTemplate($name)
    {
        $template = new Template();
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
     * @param array $args List of new GET arguments
     *
     * @return string
     */
    public function url($args = [])
    {
        $url = $_SERVER['REQUEST_URI'];
        $query = parse_url($url, PHP_URL_QUERY);

        $args = http_build_query($args);

        if ($query) {
            $url .= '&'.$args;
        } else {
            $url .= '?'.$args;
        }

        return $url;
    }
}
