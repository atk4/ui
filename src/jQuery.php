<?php

namespace atk4\ui;

/**
 * Implements mapper for jQuery library.
 *
 * @method jQuery append()
 * @method jQuery appendTo()
 * @method jQuery bind()
 * @method jQuery click()
 * @method jQuery on()
 * @method jQuery attr()
 * @method jQuery val()
 * @method jQuery prop()
 * @method jQuery data()
 * @method jQuery confirm()
 * @method jQuery submit()
 * @method jQuery find()
 * @method jQuery select()
 * @method jQuery focus()
 * @method jQuery css()
 * @method jQuery change()
 * @method jQuery trigger()
 * @method jQuery location()
 * @method jQuery closest()
 * @method jQuery show()
 * @method jQuery hide()
 * @method jQuery toggle()
 * @method jQuery parent()
 * @method jQuery addClass()
 * @method jQuery removeClass()
 * @method jQuery toggleClass()
 * @method jQuery position()
 *
 * Extensions by SemanticUI
 * @method jQuery form()
 * @method jQuery api()
 * @method jQuery visibility()
 */
class jQuery extends jsChain
{
    public $_include = 'jquery.min.js';
    public $_version = '3.1.1';
    public $_integrity = 'sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=';
    public $_library = '$';

    /**
     * @argument $selector
     * @argument $context
     */
    public function __construct(...$constructorArgs)
    {
        parent::__construct();

        $this->_constructorArgs = $constructorArgs;
    }

    public function univ()
    {
        return new jUniv($this);
    }
}
