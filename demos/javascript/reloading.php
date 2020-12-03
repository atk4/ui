<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

// Test 1 - Basic reloading
\Atk4\Ui\Header::addTo($app, ['Button reloading segment']);
$v = \Atk4\Ui\View::addTo($app, ['ui' => 'segment'])->set((string) random_int(1, 100));
\Atk4\Ui\Button::addTo($app, ['Reload random number'])->js('click', new \Atk4\Ui\JsReload($v, [], new \Atk4\Ui\JsExpression('console.log("Output with afterSuccess");')));

// Test 2 - Reloading self
\Atk4\Ui\Header::addTo($app, ['JS-actions will be re-applied']);
$b2 = \Atk4\Ui\Button::addTo($app, ['Reload Myself']);
$b2->js('click', new \Atk4\Ui\JsReload($b2));

// Test 3 - avoid duplicate
\Atk4\Ui\Header::addTo($app, ['No duplicate JS bindings']);
$b3 = \Atk4\Ui\Button::addTo($app, ['Reload other button']);
$b4 = \Atk4\Ui\Button::addTo($app, ['Add one dot']);

$b4->js('click', $b4->js()->text(new \Atk4\Ui\JsExpression('[]+"."', [$b4->js()->text()])));
$b3->js('click', new \Atk4\Ui\JsReload($b4));

// Test 3 - avoid duplicate
\Atk4\Ui\Header::addTo($app, ['Make sure nested JS bindings are applied too']);
$seg = \Atk4\Ui\View::addTo($app, ['ui' => 'segment']);

// add 3 counters
Counter::addTo($seg);
Counter::addTo($seg, ['40']);
Counter::addTo($seg, ['-20']);

// Add button to reload all counters
$bar = \Atk4\Ui\View::addTo($app, ['ui' => 'buttons']);
$b = \Atk4\Ui\Button::addTo($bar, ['Reload counter'])->js('click', new \Atk4\Ui\JsReload($seg));

// Relading with argument
\Atk4\Ui\Header::addTo($app, ['We can pass argument to reloader']);

$v = \Atk4\Ui\View::addTo($app, ['ui' => 'segment'])->set($_GET['val'] ?? 'No value');

\Atk4\Ui\Button::addTo($app, ['Set value to "hello"'])->js('click', new \Atk4\Ui\JsReload($v, ['val' => 'hello']));
\Atk4\Ui\Button::addTo($app, ['Set value to "world"'])->js('click', new \Atk4\Ui\JsReload($v, ['val' => 'world']));

$val = \Atk4\Ui\Form\Control\Line::addTo($app, ['']);
$val->addAction('Set Custom Value')->js('click', new \Atk4\Ui\JsReload($v, ['val' => $val->jsInput()->val()], $val->jsInput()->focus()));
