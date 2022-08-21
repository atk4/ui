<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Button;
use Atk4\Ui\Header;
use Atk4\Ui\JsExpression;
use Atk4\Ui\JsReload;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

// Test 1 - Basic reloading
Header::addTo($app, ['Button reloading segment']);
$v = View::addTo($app, ['ui' => 'segment'])->set((string) random_int(1, 100));
Button::addTo($app, ['Reload random number'])->js('click', new JsReload($v, [], new JsExpression('console.log("Output with afterSuccess");')));

// Test 2 - Reloading self
Header::addTo($app, ['JS-actions will be re-applied']);
$b2 = Button::addTo($app, ['Reload Myself']);
$b2->js('click', new JsReload($b2));

// Test 3 - avoid duplicate
Header::addTo($app, ['No duplicate JS bindings']);
$b3 = Button::addTo($app, ['Reload other button']);
$b4 = Button::addTo($app, ['Add one dot']);

$b4->js('click', $b4->js()->text(new JsExpression('[]+"."', [$b4->js()->text()])));
$b3->js('click', new JsReload($b4));

// Test 3 - avoid duplicate
Header::addTo($app, ['Make sure nested JS bindings are applied too']);
$seg = View::addTo($app, ['ui' => 'segment']);

// add 3 counters
Counter::addTo($seg);
Counter::addTo($seg, ['40']);
Counter::addTo($seg, ['-20']);

// Add button to reload all counters
$bar = View::addTo($app, ['ui' => 'buttons']);
$b = Button::addTo($bar, ['Reload counter'])->js('click', new JsReload($seg));

// Relading with argument
Header::addTo($app, ['We can pass argument to reloader']);

$v = View::addTo($app, ['ui' => 'segment'])->set($_GET['val'] ?? 'No value');

Button::addTo($app, ['Set value to "hello"'])->js('click', new JsReload($v, ['val' => 'hello']));
Button::addTo($app, ['Set value to "world"'])->js('click', new JsReload($v, ['val' => 'world']));

$val = \Atk4\Ui\Form\Control\Line::addTo($app, ['']);
$val->addAction('Set Custom Value')->js('click', new JsReload($v, ['val' => $val->jsInput()->val()], $val->jsInput()->focus()));
