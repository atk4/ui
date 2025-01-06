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
use Atk4\Ui\ViewWithContent;

/** @var App $app */
require_once __DIR__ . '/../init-app.php';

$app->layout->js(true, new JsExpression('atk.i = 0'));
$reloadUrlArgs = ['i' => new JsExpression('++atk.i')];

$logInput = View::addTo($app, ['element' => 'input', 'name' => 'log']);
$logInput->setStyle('width', '100%');

$setBoxTextAndStyleFx = static function (ViewWithContent $view, string $text) {
    $view->set($text);
    $view->setStyle('width', 'fit-content');
    $view->setStyle('padding', '5px');
    $view->setStyle('margin', '5px');
    $view->setStyle('border', '1px solid black');
};

$addBoxFx = static function ($owner, string $name) use ($setBoxTextAndStyleFx, $logInput) {
    $box = ViewWithContent::addTo($owner);
    $setBoxTextAndStyleFx($box, $name . (int) $box->getApp()->tryGetRequestQueryParam('i'));

    $box->js(true, new JsExpression(<<<'JS'
        var target = document.querySelector([target]);
        var logInput = document.querySelector([logInput]);
        logInput.value = (logInput.value + ' ').trimStart() + target.lastChild.textContent;
        JS, ['target' => $box, 'logInput' => $logInput]));

    return $box;
};

$viewA = $addBoxFx($app, 'A');
$viewI = $addBoxFx($viewA, 'I');
$viewJ = $addBoxFx($viewA, 'J');
$viewU = $addBoxFx($viewI, 'U');
$viewV = $addBoxFx($viewI, 'V');

$makeAddHandlerJsFx = static function (View $view) use ($logInput) {
    return new JsExpression(<<<'JS'
        const target = document.querySelector([target]);
        const logInput = document.querySelector([logInput]);
        atk.lastTarget = target;
        atk.lastHandler = () => logInput.value = (logInput.value + ' ').trimStart() + 'h' + target.lastChild.textContent;
        atk.elementRemoveObserver.addHandler(atk.lastTarget, atk.lastHandler);
        JS, ['target' => $view, 'logInput' => $logInput]);
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

$apiContext = ViewWithContent::addTo($app);
$setBoxTextAndStyleFx($apiContext, 'stateContext');
$apiButton = Button::addTo($app, ['Run slow API']);
$apiButton->on('click', static function () use ($apiButton) {
    sleep(1);

    return $apiButton->js()->text('Abort failed');
}, ['apiConfig' => ['stateContext' => $apiContext]]);
Button::addTo($app, ['Run slow API & remove'])->on('click', new JsBlock([
    $apiButton->js()->click(),
    new JsExpression('document.querySelector([elem]).remove()', ['elem' => $apiContext]),
]));

Header::addTo($app, ['SSE']);

$sseContext = ViewWithContent::addTo($app);
$sse = JsSse::addTo($app, ['stateContext' => $sseContext]);
$setBoxTextAndStyleFx($sseContext, 'stateContext');
$sseButton = Button::addTo($app, ['Run slow SSE']);
$sseButton->on('click', $sse->set(static function () use ($sse, $sseButton) {
    sleep(1);
    $sse->send($sseButton->js()->text('Abort failed'));
}), ['apiConfig' => ['stateContext' => $sseContext]]);
Button::addTo($app, ['Run slow SSE & remove'])->on('click', new JsBlock([
    $sseButton->js()->click(),
    new JsExpression('document.querySelector([elem]).remove()', ['elem' => $sseContext]),
]));
