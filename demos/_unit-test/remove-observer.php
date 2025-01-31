<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\App;
use Atk4\Ui\Button;
use Atk4\Ui\Header;
use Atk4\Ui\Js\JsBlock;
use Atk4\Ui\Js\JsExpression;
use Atk4\Ui\JsSse;
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
        atk.lastTarget = target;
        atk.lastHandler = () => logInput.value = (logInput.value + ' ').trimStart() + 'h' + target.lastChild.textContent;
        atk.elementRemoveObserver.addHandler(atk.lastTarget, atk.lastHandler);
        JS, ['target' => $view, 'log' => $log]);
};

Button::addTo($app, ['Add A handler'])->on('click', $makeAddHandlerJsFx($viewA));
Button::addTo($app, ['Add I handler'])->on('click', $makeAddHandlerJsFx($viewI));
Button::addTo($app, ['Add U handler'])->on('click', $makeAddHandlerJsFx($viewU));
Button::addTo($app, ['Add V handler'])->on('click', $makeAddHandlerJsFx($viewV));
Button::addTo($app, ['Remove last handler'])->on('click', new JsExpression('atk.elementRemoveObserver.removeHandler(atk.lastTarget, atk.lastHandler)'));

Button::addTo($app, ['Reload I'])->on('click', $viewI->jsReload($reloadUrlArgs));
Button::addTo($app, ['Reload J'])->on('click', $viewJ->jsReload($reloadUrlArgs));
Button::addTo($app, ['Reload U'])->on('click', $viewU->jsReload($reloadUrlArgs));
Button::addTo($app, ['Reload V'])->on('click', $viewV->jsReload($reloadUrlArgs));

Button::addTo($app, ['Move U to J'])->on('click', new JsExpression(<<<'JS'
    const elem = document.querySelector([elem]);
    const newParent = document.querySelector([newParent]);
    elem.remove();
    newParent.prepend(elem);
    JS, ['elem' => $viewU, 'newParent' => $viewJ]));
Button::addTo($app, ['Readd U'])->on('click', new JsExpression(<<<'JS'
    const elem = document.querySelector([elem]);
    const parentElem = elem.parentElement;
    elem.remove();
    parentElem.prepend(elem);
    JS, ['elem' => $viewU]));

Header::addTo($app, ['API']);

$apiView = View::addTo($app);
$apiButton = Button::addTo($apiView, ['Run slow API']);
$apiButton->on('click', static function () use ($apiButton) {
    sleep(1);

    return $apiButton->js()->text('Abort failed');
});
Button::addTo($apiView, ['Run slow API & reload'])->on('click', new JsBlock([
    $apiButton->js()->click(),
    $apiView->jsReload(),
]));

Header::addTo($app, ['SSE']);

$sseView = View::addTo($app);
$sse = JsSse::addTo($sseView);
$sseButton = Button::addTo($sseView, ['Run slow SSE']);
$sseButton->on('click', $sse->set(static function () use ($sse, $sseButton) {
    sleep(1);
    $sse->send($sseButton->js()->text('Abort failed'));
}));
Button::addTo($sseView, ['Run slow SSE & reload'])->on('click', new JsBlock([
    $sseButton->js()->click(),
    $sseView->jsReload(),
]));
