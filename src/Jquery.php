<?php

declare(strict_types=1);

namespace Atk4\Ui;

/**
 * Implements mapper for jQuery library. Following mappings are just to keep phpstan/PhpStorm happy.
 *
 * @method Jquery accordion(...$args)
 * @method Jquery addClass(...$args)
 * @method Jquery append(...$args)
 * @method Jquery appendTo(...$args)
 * @method Jquery attr(...$args)
 * @method Jquery change(...$args)
 * @method Jquery checkbox(...$args)
 * @method Jquery click(...$args)
 * @method Jquery closest(...$args)
 * @method Jquery confirm(...$args)
 * @method Jquery css(...$args)
 * @method Jquery data(...$args)
 * @method Jquery dropdown(...$args)
 * @method Jquery fadeOut(...$args)
 * @method Jquery find(...$args)
 * @method Jquery first(...$args)
 * @method Jquery flatpickr(...$args)
 * @method Jquery focus(...$args)
 * @method Jquery get(...$args)
 * @method Jquery height(...$args)
 * @method Jquery hide(...$args)
 * @method Jquery html(...$args)
 * @method Jquery location(...$args)
 * @method Jquery modal(...$args)
 * @method Jquery off(...$args)
 * @method Jquery on(string $events, ...$args)
 * @method Jquery parent(...$args)
 * @method Jquery parents(...$args)
 * @method Jquery popup(...$args)
 * @method Jquery position(...$args)
 * @method Jquery progress(...$args)
 * @method Jquery prop(...$args)
 * @method Jquery rating(...$args)
 * @method Jquery ready(...$args)
 * @method Jquery reload(...$args)
 * @method Jquery removeAttr(...$args)
 * @method Jquery removeClass(...$args)
 * @method Jquery removeData(...$args)
 * @method Jquery select(...$args)
 * @method Jquery serialize(...$args)
 * @method Jquery show(...$args)
 * @method Jquery submit(...$args)
 * @method Jquery tab(...$args)
 * @method Jquery text(...$args)
 * @method Jquery toast(...$args)
 * @method Jquery toggle(...$args)
 * @method Jquery toggleClass(...$args)
 * @method Jquery transition(...$args)
 * @method Jquery trigger(...$args)
 * @method Jquery val(...$args)
 *
 * For Fomantic-UI:
 * @method Jquery api(...$args)
 * @method Jquery form(...$args)
 * @method Jquery visibility(...$args)
 *
 * For atk4/ui JS (defined in js/src/plugin.js):
 * @method Jquery atkAjaxec(...$args)
 * @method Jquery atkColumnResizer(...$args)
 * @method Jquery atkConditionalForm(...$args)
 * @method Jquery atkConfirm(...$args)
 * @method Jquery atkCreateModal(...$args)
 * @method Jquery atkFileUpload(...$args)
 * @method Jquery atkJsSearch(...$args)
 * @method Jquery atkJsSortable(...$args)
 * @method Jquery atkReloadView(...$args)
 * @method Jquery atkScroll(...$args)
 * @method Jquery atkServerEvent(...$args)
 * @method Jquery atkSidenav(...$args)
 */
class Jquery extends JsChain
{
    public $_library = '$';

    /**
     * @param mixed ...$constructorArgs arguments for JavaScript jQuery constructor
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
