<?php

require_once __DIR__ . '/../atk-init.php';
require_once __DIR__ . '/../_includes/Counter.php';

// Test 1 - Basic reloading
\atk4\ui\Header::addTo($app, ['Button reloading segment']);
$v = \atk4\ui\View::addTo($app, ['ui' => 'segment'])->set((string) rand(1, 100));
\atk4\ui\Button::addTo($app, ['Reload random number'])->js('click', new \atk4\ui\jsReload($v, [], new \atk4\ui\jsExpression('console.log("Output with afterSuccess");')));

// Test 2 - Reloading self
\atk4\ui\Header::addTo($app, ['JS-actions will be re-applied']);
$b2 = \atk4\ui\Button::addTo($app, ['Reload Myself']);
$b2->js('click', new \atk4\ui\jsReload($b2));

// Test 3 - avoid duplicate
\atk4\ui\Header::addTo($app, ['No duplicate JS bindings']);
$b3 = \atk4\ui\Button::addTo($app, ['Reload other button']);
$b4 = \atk4\ui\Button::addTo($app, ['Add one dot']);

$b4->js('click', $b4->js()->text(new \atk4\ui\jsExpression('[]+"."', [$b4->js()->text()])));
$b3->js('click', new \atk4\ui\jsReload($b4));

// Test 3 - avoid duplicate
\atk4\ui\Header::addTo($app, ['Make sure nested JS bindings are applied too']);
$seg = \atk4\ui\View::addTo($app, ['ui' => 'segment']);

// Add 3 counters from '_includes/Counter.php'
Counter::addTo($seg);
Counter::addTo($seg, '40');
Counter::addTo($seg, '-20');

// Add button to reload all counters
$bar = \atk4\ui\View::addTo($app, ['ui' => 'buttons']);
$b = \atk4\ui\Button::addTo($bar, ['Reload counter'])->js('click', new \atk4\ui\jsReload($seg));

// Relading with argument
\atk4\ui\Header::addTo($app, ['We can pass argument to reloader']);

$v = \atk4\ui\View::addTo($app, ['ui' => 'segment'])->set($_GET['val'] ?? 'No value');

\atk4\ui\Button::addTo($app, ['Set value to "hello"'])->js('click', new \atk4\ui\jsReload($v, ['val' => 'hello']));
\atk4\ui\Button::addTo($app, ['Set value to "world"'])->js('click', new \atk4\ui\jsReload($v, ['val' => 'world']));

$val = \atk4\ui\FormField\Line::addTo($app, ['']);
$val->addAction('Set Custom Value')->js('click', new \atk4\ui\jsReload($v, ['val' => $val->jsInput()->val()], $val->jsInput()->focus()));
