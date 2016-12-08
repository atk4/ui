<?php

namespace atk4\ui;

class App {
    use \atk4\core\InitializerTrait {
        init as _init;
    }

    public $template_dir = null;

    public $skin = 'semantic-ui';

    public function __construct($defaults = [])
    {
        if (!is_array($defaults)) {
            throw new Exception(['Constructor requires array argument', 'arg' => $defaults]);
        }
        foreach ($defaults as $key => $val) {
            if (is_array($val)) {
                $this->$key = array_merge(isset($this->$key) && is_array($this->$key) ? $this->$key : [], $val);
            } elseif(!is_null($val)) {
                $this->$key = $val;
            }
        }
    }

    function init() {
        $this->_init();
        $this->template_dir = dirname(dirname(__FILE__)).'/template/'.$this->skin;
    }

    function loadTemplate($name) {
        $template = new Template();
        if(in_array($name[0], ['.','/','\\'])) {
            $template->load($name);
        }else {
            $template->load($this->template_dir.'/'.$name);
        }
        return $template;
    }
}
