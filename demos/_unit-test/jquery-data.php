<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\App;
use Atk4\Ui\Button;
use Atk4\Ui\Js\Jquery;
use Atk4\Ui\Js\JsCallbackLoadableValue;
use Atk4\Ui\Js\JsExpression;
use Atk4\Ui\Js\JsToast;
use Atk4\Ui\View;

/** @var App $app */
require_once __DIR__ . '/../init-app.php';

$view = View::addTo($app);
$view->setAttr('data-foo--a', 'false');
$view->setAttr('data-bar', 'x');

Button::addTo($app, ['Display type'])->on('click', static function (Jquery $j, $type) {
    return new JsToast('Type: ' . $type);
}, [new JsCallbackLoadableValue(new JsExpression('typeof $([elem]).data()[\'foo-A\'] + \', \' + typeof $([elem]).data(\'foo--a\')', ['elem' => $view]), static fn ($v) => $v)]);
Button::addTo($app, ['Call $elem.data(k, 5)'])->on('click', new JsExpression('$([elem]).data(\'foo--a\', 5)', ['elem' => $view]));
Button::addTo($app, ['Call $elem.removeData()'])->on('click', new JsExpression('$([elem]).removeData()', ['elem' => $view]));
Button::addTo($app, ['Call $elem.removeData(k)'])->on('click', new JsExpression('$([elem]).removeData(\'foo--a\')', ['elem' => $view]));
