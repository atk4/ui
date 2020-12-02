<?php

declare(strict_types=1);

namespace Atk4\Ui;

/**
 * Implements mapper for jQuery library. Following mappings are just to keep PhpStorm happy.
 *
 * @method Jquery addClass()
 * @method Jquery append()
 * @method Jquery appendTo()
 * @method Jquery attr()
 * @method Jquery bind(...$args)
 * @method Jquery change()
 * @method Jquery click()
 * @method Jquery closest()
 * @method Jquery confirm()
 * @method Jquery css()
 * @method Jquery data($a = null, $b = null)
 * @method Jquery find()
 * @method Jquery focus()
 * @method Jquery hide()
 * @method Jquery html($t = null)
 * @method Jquery location()
 * @method Jquery on($a = null, $b = null, $c = null)
 * @method Jquery parent()
 * @method Jquery position()
 * @method Jquery prop()
 * @method Jquery removeClass()
 * @method Jquery select()
 * @method Jquery show()
 * @method Jquery submit()
 * @method Jquery text($t = null)
 * @method Jquery toggle()
 * @method Jquery toggleClass()
 * @method Jquery trigger()
 * @method Jquery val()
 *
 * Extensions by SemanticUI
 * @method Jquery api($a = null)
 * @method Jquery form($a = null)
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
}
