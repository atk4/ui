<?php

/**
 * Class represents a miniature app.
 */
class MiniApp
{
    use \atk4\core\InitializerTrait;    // init() method is for your service

    public $template_path = null;

    public function __construct($defaults = [])
    {
        if (!is_array($defaults)) {
            throw new Exception([
                '$defaults must be specified as an array',
                'arg' => $defaults,
            ]);
        }

        foreach ($defaults as $key => $val) {
            /*

            // If argument is array and default value is array, merge both
            if (is_array($val) && isset($this->$key) && is_array($this->$key)) {
                $this->$key = array_merge($this->$key, $val);
            } else {
             */
                $this->$key = $val;
            //}
        }
    }
}
