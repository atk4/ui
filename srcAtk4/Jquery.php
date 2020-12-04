<?php

declare(strict_types=1);

namespace Atk4\Ui;

/**
 * Implements mapper for jQuery library. Following mappings are just to keep PhpStorm happy.
 *
 * @method Jquery append()
 * @method Jquery appendTo()
 * @method Jquery bind(...$args)
 * @method Jquery click()
 * @method Jquery on($a=null,$b=null,$c=null)
 * @method Jquery attr()
 * @method Jquery val()
 * @method Jquery prop()
 * @method Jquery data($a=null,$b=null)
 * @method Jquery confirm()
 * @method Jquery submit()
 * @method Jquery find()
 * @method Jquery select()
 * @method Jquery focus()
 * @method Jquery css()
 * @method Jquery change()
 * @method Jquery trigger()
 * @method Jquery location()
 * @method Jquery closest()
 * @method Jquery show()
 * @method Jquery hide()
 * @method Jquery toggle()
 * @method Jquery parent()
 * @method Jquery addClass()
 * @method Jquery removeClass()
 * @method Jquery toggleClass()
 * @method Jquery position()
 * @method Jquery text($t=null)
 * @method Jquery html($t=null)
 *
 * Extensions by SemanticUI
 * @method Jquery form($a=null)
 * @method Jquery api($a=null)
 * @method Jquery visibility()
 */
class Jquery extends JsChain
{
    public $_include = 'jquery.min.js';
    public $_version = '3.1.1';
    public $_integrity = 'sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=';
    public $_library = '$';

    /**
     * Params for this constructior will be passed on to jQuery() in JavaScript.
     * Start with: JsExpressionable|View|string $selector such as '.myclass' or $view.
     * Second argument would be $context. See jQuery manual for more info.
     *
     * @param array $constructorArgs - passes $selector and $context to jQuery(..)
     */
    public function __construct(...$constructorArgs)
    {
        parent::__construct();

        if (count($constructorArgs) === 0) {
            $constructorArgs = [new JsExpression('this')];
        }

        $this->_constructorArgs = $constructorArgs;
    }

    public function univ()
    {
        return new Juniv($this);
    }
}
