<?php

namespace atk4\ui;

/**
 * Implements mapper for jQuery library. Following mappings are just to keep PhpStorm happy.
 *
 * @method jQuery append()
 * @method jQuery appendTo()
 * @method jQuery bind(...$args)
 * @method jQuery click()
 * @method jQuery on($a=null,$b=null,$c=null)
 * @method jQuery attr()
 * @method jQuery val()
 * @method jQuery prop()
 * @method jQuery data($a=null,$b=null)
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
 * @method jQuery text($t=null)
 * @method jQuery html($t=null)
 *
 * Extensions by SemanticUI
 * @method jQuery form($a=null)
 * @method jQuery api($a=null)
 * @method jQuery visibility()
 */
class jQuery extends jsChain
{
    public $_include = 'jquery.min.js';
    public $_version = '3.1.1';
    public $_integrity = 'sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=';
    public $_library = '$';

    /**
     * Params for this constructior will be passed on to jQuery() in JavaScript.
     * Start with: jsExpressionable|View|string $selector such as '.myclass' or $view.
     * Second argument would be $context. See jQuery manual for more info.
     *
     * @param array $constructorArgs - passes $selector and $context to jQuery(..)
     */
    public function __construct(...$constructorArgs)
    {
        parent::__construct();

        if ($constructorArgs == []) {
            $constructorArgs = [new jsExpression('this')];
        }

        $this->_constructorArgs = $constructorArgs;
    }

    public function univ()
    {
        return new jUniv($this);
    }
}
