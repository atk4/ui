<?php

declare(strict_types=1);

namespace Atk4\Ui\Js;

/**
 * Same as JsChain class, but with annotated methods to make phpstan/PhpStorm happy.
 *
 * For jQuery:
 *
 * @method Jquery  addClass(...$args)
 * @method Jquery  append(...$args)
 * @method Jquery  appendTo(...$args)
 * @method JsChain attr(...$args)
 * @method Jquery  change(...$args)
 * @method Jquery  click(...$args)
 * @method Jquery  closest(...$args)
 * @method JsChain css(...$args)
 * @method JsChain data(...$args)
 * @method Jquery  each(...$args)
 * @method Jquery  fadeOut(...$args)
 * @method Jquery  find(...$args)
 * @method Jquery  first(...$args)
 * @method Jquery  focus(...$args)
 * @method JsChain get(...$args)
 * @method JsChain height(...$args)
 * @method Jquery  hide(...$args)
 * @method JsChain html(...$args)
 * @method Jquery  map(...$args)
 * @method Jquery  off(...$args)
 * @method Jquery  on(string $events, ...$args)
 * @method Jquery  parent(...$args)
 * @method Jquery  parents(...$args)
 * @method JsChain position(...$args)
 * @method JsChain prop(...$args)
 * @method Jquery  removeAttr(...$args)
 * @method Jquery  removeClass(...$args)
 * @method Jquery  removeData(...$args)
 * @method Jquery  select(...$args)
 * @method JsChain serialize(...$args)
 * @method Jquery  show(...$args)
 * @method Jquery  submit(...$args)
 * @method JsChain text(...$args)
 * @method Jquery  toggle(...$args)
 * @method Jquery  toggleClass(...$args)
 * @method Jquery  trigger(...$args)
 * @method JsChain val(...$args)
 *
 * For Fomantic-UI:
 * @method Jquery accordion(...$args)
 * @method Jquery api(...$args)
 * @method Jquery checkbox(...$args)
 * @method Jquery confirm(...$args)
 * @method Jquery dropdown(...$args)
 * @method Jquery form(...$args)
 * @method Jquery modal(...$args)
 * @method Jquery popup(...$args)
 * @method Jquery progress(...$args)
 * @method Jquery rating(...$args)
 * @method Jquery tab(...$args)
 * @method Jquery toast(...$args)
 * @method Jquery transition(...$args)
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
 *
 * For other:
 * @method Jquery flatpickr(...$args)
 */
class Jquery extends JsChain
{
    public string $_library = '$';

    /**
     * @param mixed ...$constructorArgs arguments for JavaScript jQuery constructor
     */
    public function __construct(...$constructorArgs)
    {
        parent::__construct($this->_library);

        if (count($constructorArgs) === 0) {
            $constructorArgs = [new JsExpression('this')];
        }

        $this->_constructorArgs = $constructorArgs;
    }
}
