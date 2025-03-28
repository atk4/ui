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
$view->setAttr('data-bar', 'null');

Button::addTo($app, ['Display types'])->on('click', static function (Jquery $j, $type) {
    return new JsToast('Types: ' . $type);
}, [new JsCallbackLoadableValue(new JsExpression('[\'foo-A\', \'bar\'].map((k) => typeof $([elem]).data()[ k] + \'/\' + typeof $([elem]).data(k)).join(\', \')', ['elem' => $view]), static fn ($v) => $v)]);
Button::addTo($app, ['Call $elem.data(k, int)'])->on('click', new JsExpression('$([elem]).data(\'foo--a\', 0)', ['elem' => $view]));
Button::addTo($app, ['Call $elem.data({k: bigint})'])->on('click', new JsExpression('$([elem]).data({\'foo--a\': 0n})', ['elem' => $view]));
Button::addTo($app, ['Call $elem.removeData()'])->on('click', new JsExpression('$([elem]).removeData()', ['elem' => $view]));
Button::addTo($app, ['Call $elem.removeData(k)'])->on('click', new JsExpression('$([elem]).removeData(\'foo--a\')', ['elem' => $view]));
