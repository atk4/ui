<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\App;
use Atk4\Ui\Button;
use Atk4\Ui\Js\JsExpression;
use Atk4\Ui\View;

/** @var App $app */
require_once __DIR__ . '/../init-app.php';

$app->layout->js(true, new JsExpression('atk.i = 0'));
$reloadUrlArgs = ['i' => new JsExpression('++atk.i')];

$log = View::addTo($app, ['element' => 'input', 'name' => 'log']);
$log->setStyle('width', '100%');

$addBoxFx = static function ($owner, string $name) use ($log) {
    $box = View::addTo($owner);
    $box->setStyle('padding', '5px');
    $box->setStyle('margin', '5px');
    $box->setStyle('border', '1px solid black');

    $box->set($name . (int) $box->getApp()->tryGetRequestQueryParam('i'));

    $box->js(true, new JsExpression(<<<'JS'
        var target = document.querySelector([target]);
        var logInput = document.querySelector([log]);
        logInput.value = (logInput.value + ' ').trimStart() + target.lastChild.textContent;
        JS, ['target' => $box, 'log' => $log]));

    return $box;
};

$viewA = $addBoxFx($app, 'A');
$viewI = $addBoxFx($viewA, 'I');
$viewJ = $addBoxFx($viewA, 'J');
$viewU = $addBoxFx($viewI, 'U');
$viewV = $addBoxFx($viewI, 'V');

$makeAddHandlerJsFx = static function (View $view) use ($log) {
    return new JsExpression(<<<'JS'
        const target = document.querySelector([target]);
        const logInput = document.querySelector([log]);
        atk.elementRemoveObserver.addHandler(target, () => logInput.value = (logInput.value + ' ').trimStart() + 'h' + target.lastChild.textContent);
        JS, ['target' => $view, 'log' => $log]);
};

Button::addTo($app, ['Add A handler'])->on('click', $makeAddHandlerJsFx($viewA));
Button::addTo($app, ['Add I handler'])->on('click', $makeAddHandlerJsFx($viewI));
Button::addTo($app, ['Add V handler'])->on('click', $makeAddHandlerJsFx($viewV));

Button::addTo($app, ['Reload I'])->on('click', $viewI->jsReload($reloadUrlArgs));
Button::addTo($app, ['Reload V'])->on('click', $viewV->jsReload($reloadUrlArgs));
