<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\App;
use Atk4\Ui\Button;
use Atk4\Ui\Js\JsExpression;
use Atk4\Ui\Js\JsCallbackLoadableValue;
use Atk4\Ui\Js\JsToast;
use Atk4\Ui\Js\Jquery;
use Atk4\Ui\View;

/** @var App $app */
require_once __DIR__ . '/../init-app.php';

$view = View::addTo($app);
$view->setAttr('data-foo', 'false');
$view->setAttr('data-bar', 'x');

Button::addTo($app, ['Display type'])->on('click', static function (Jquery $j, $type) {
    return new JsToast('Type: ' . $type);
}, [new JsCallbackLoadableValue(new JsExpression('typeof $([elem]).data()[\'foo\'] + \', \' + typeof $([elem]).data(\'foo\')', ['elem' => $view]), static fn ($v) => $v)]);
Button::addTo($app, ['Call $elem.data(\'foo\', 5)'])->on('click', new JsExpression('$([elem]).data(\'foo\', 5)', ['elem' => $view]));
Button::addTo($app, ['Call $elem.removeData()'])->on('click', new JsExpression('$([elem]).removeData()', ['elem' => $view]));
Button::addTo($app, ['Call $elem.removeData(\'foo\')'])->on('click', new JsExpression('$([elem]).removeData(\'foo\')', ['elem' => $view]));
