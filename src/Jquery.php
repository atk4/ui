<?php

declare(strict_types=1);

namespace Atk4\Ui;

/**
 * Implements mapper for jQuery library. Following mappings are just to keep PhpStorm happy.
 *
 * @method Jquery addClass(...$args)
 * @method Jquery append(...$args)
 * @method Jquery appendTo(...$args)
 * @method Jquery attr(...$args)
 * @method Jquery bind(string $eventType, ...$args)
 * @method Jquery change(...$args)
 * @method Jquery click(...$args)
 * @method Jquery closest(...$args)
 * @method Jquery confirm(...$args)
 * @method Jquery css(...$args)
 * @method Jquery data(...$args)
 * @method Jquery find(...$args)
 * @method Jquery focus(...$args)
 * @method Jquery hide(...$args)
 * @method Jquery html(...$args)
 * @method Jquery location(...$args)
 * @method Jquery on(string $events, ...$args)
 * @method Jquery parent(...$args)
 * @method Jquery position(...$args)
 * @method Jquery prop(...$args)
 * @method Jquery removeClass(...$args)
 * @method Jquery select(...$args)
 * @method Jquery show(...$args)
 * @method Jquery submit(...$args)
 * @method Jquery text(...$args)
 * @method Jquery toggle(...$args)
 * @method Jquery toggleClass(...$args)
 * @method Jquery trigger(...$args)
 * @method Jquery val(...$args)
 *
 * Extensions by SemanticUI
 * @method Jquery api(...$args)
 * @method Jquery form(...$args)
 * @method Jquery visibility(...$args)
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
