<?php

declare(strict_types=1);

namespace atk4\ui\demo;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

// Test 1 - Basic reloading
\atk4\ui\Header::addTo($app, ['Button reloading segment']);
$v = \atk4\ui\View::addTo($app, ['ui' => 'segment'])->set((string) random_int(1, 100));
\atk4\ui\Button::addTo($app, ['Reload random number'])->js('click', new \atk4\ui\JsReload($v, [], new \atk4\ui\JsExpression('console.log("Output with afterSuccess");')));

// Test 2 - Reloading self
\atk4\ui\Header::addTo($app, ['JS-actions will be re-applied']);
$b2 = \atk4\ui\Button::addTo($app, ['Reload Myself']);
$b2->js('click', new \atk4\ui\JsReload($b2));

// Test 3 - avoid duplicate
\atk4\ui\Header::addTo($app, ['No duplicate JS bindings']);
$b3 = \atk4\ui\Button::addTo($app, ['Reload other button']);
$b4 = \atk4\ui\Button::addTo($app, ['Add one dot']);

$b4->js('click', $b4->js()->text(new \atk4\ui\JsExpression('[]+"."', [$b4->js()->text()])));
$b3->js('click', new \atk4\ui\JsReload($b4));

// Test 3 - avoid duplicate
\atk4\ui\Header::addTo($app, ['Make sure nested JS bindings are applied too']);
$seg = \atk4\ui\View::addTo($app, ['ui' => 'segment']);

// add 3 counters
Counter::addTo($seg);
Counter::addTo($seg, '40');
Counter::addTo($seg, '-20');

// Add button to reload all counters
$bar = \atk4\ui\View::addTo($app, ['ui' => 'buttons']);
$b = \atk4\ui\Button::addTo($bar, ['Reload counter'])->js('click', new \atk4\ui\JsReload($seg));

// Relading with argument
\atk4\ui\Header::addTo($app, ['We can pass argument to reloader']);

$v = \atk4\ui\View::addTo($app, ['ui' => 'segment'])->set($_GET['val'] ?? 'No value');

\atk4\ui\Button::addTo($app, ['Set value to "hello"'])->js('click', new \atk4\ui\JsReload($v, ['val' => 'hello']));
\atk4\ui\Button::addTo($app, ['Set value to "world"'])->js('click', new \atk4\ui\JsReload($v, ['val' => 'world']));

$val = \atk4\ui\Form\Control\Line::addTo($app, ['']);
$val->addAction('Set Custom Value')->js('click', new \atk4\ui\JsReload($v, ['val' => $val->jsInput()->val()], $val->jsInput()->focus()));
