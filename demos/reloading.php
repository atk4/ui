<?php

require_once __DIR__ . '/init.php';

// Test 1 - Basic reloading
$app->add(['Header', 'Button reloading segment']);
$v = $app->add(['View', 'ui' => 'segment'])->set((string) rand(1, 100));
$app->add(['Button', 'Reload random number'])->js('click', new \atk4\ui\jsReload($v, [], new \atk4\ui\jsExpression('console.log("Output with afterSuccess");')));

// Test 2 - Reloading self
$app->add(['Header', 'JS-actions will be re-applied']);
$b2 = $app->add(['Button', 'Reload Myself']);
$b2->js('click', new \atk4\ui\jsReload($b2));

// Test 3 - avoid duplicate
$app->add(['Header', 'No duplicate JS bindings']);
$b3 = $app->add(['Button', 'Reload other button']);
$b4 = $app->add(['Button', 'Add one dot']);

$b4->js('click', $b4->js()->text(new \atk4\ui\jsExpression('[]+"."', [$b4->js()->text()])));
$b3->js('click', new \atk4\ui\jsReload($b4));

// Test 3 - avoid duplicate
$app->add(['Header', 'Make sure nested JS bindings are applied too']);
$seg = $app->add(['View', 'ui' => 'segment']);

if (!class_exists('Counter')) {
    // Re-usable component implementing counter
    class Counter extends \atk4\ui\FormField\Line
    {
        public $content = 20; // default

        public function init()
        {
            parent::init();

            $this->actionLeft = new \atk4\ui\Button(['icon' => 'minus']);
            $this->action = new \atk4\ui\Button(['icon' => 'plus']);

            $this->actionLeft->js('click', $this->jsInput()->val(new \atk4\ui\jsExpression('parseInt([])-1', [$this->jsInput()->val()])));
            $this->action->js('click', $this->jsInput()->val(new \atk4\ui\jsExpression('parseInt([])+1', [$this->jsInput()->val()])));
        }
    }
}

// Add 3 counters
Counter::addTo($seg);
Counter::addTo($seg, '40');
Counter::addTo($seg, '-20');

// Add button to reload all counters
$bar = $app->add(['View', 'ui' => 'buttons']);
$b = $bar->add(['Button', 'Reload counter'])->js('click', new \atk4\ui\jsReload($seg));

// Relading with argument
$app->add(['Header', 'We can pass argument to reloader']);

$v = $app->add(['View', 'ui' => 'segment'])->set(isset($_GET['val']) ? $_GET['val'] : 'No value');

$app->add(['Button', 'Set value to "hello"'])->js('click', new \atk4\ui\jsReload($v, ['val' => 'hello']));
$app->add(['Button', 'Set value to "world"'])->js('click', new \atk4\ui\jsReload($v, ['val' => 'world']));

$val = $app->add(['FormField/Line', '']);
$val->addAction('Set Custom Value')->js('click', new \atk4\ui\jsReload($v, ['val' => $val->jsInput()->val()], $val->jsInput()->focus()));
