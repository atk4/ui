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

    public function __construct($defaults = [])
    {
        if (is_string($defaults)) {
            $defaults = ['title'=>$defaults];
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
    }

    public function setLayout(\atk4\ui\Layout $layout)
    {
        $this->html = new View(['template'=>'html.html']);
        $this->layout = $this->html->add($layout);

        return $this;
    }

    public function add()
    {
        return call_user_func_array([$this->layout, 'add'], func_get_args());
    }

    public function run()
    {
        $this->html->set('title', $this->title);
        echo $this->html->render();
    }

    public function init()
    {
        $this->_init();
        $this->template_dir = dirname(dirname(__FILE__)).'/template/'.$this->skin;
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
}
