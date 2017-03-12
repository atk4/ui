<?php

require 'init.php';

// Test 1 - Basic reloading
$layout->add(['Header', 'Button reloading segment']);
$v = $layout->add(['View', 'ui'=>'segment'])->set((string) rand(1, 100));
$layout->add(['Button', 'Reload random number'])->js('click', new \atk4\ui\jsReload($v));

// Test 2 - Reloading self
$layout->add(['Header', 'JS-actions will be re-applied']);
$b2 = $layout->add(['Button', 'Reload Myself']);
$b2->js('click', new \atk4\ui\jsReload($b2));

// Test 3 - avoid duplicate
$layout->add(['Header', 'No duplicate JS bindings']);
$b3 = $layout->add(['Button', 'Reload other button']);
$b4 = $layout->add(['Button', 'Add one dot']);

$b3->js('click', new \atk4\ui\jsReload($b4));
$b4->js('click', $b4->js()->text(new \atk4\ui\jsExpression('[]+"."', [$b4->js()->text()])));
