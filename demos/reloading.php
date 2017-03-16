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

$b4->js('click', $b4->js()->text(new \atk4\ui\jsExpression('[]+"."', [$b4->js()->text()])));
$b3->js('click', new \atk4\ui\jsReload($b4));

// Test 3 - avoid duplicate
$layout->add(['Header', 'Make sure nested JS bindings are applied too']);
$seg = $layout->add(['View', 'ui'=>'segment']);

// Re-usable component implementing counter
class Counter extends \atk4\ui\FormField\Line {

    public $content = 20; // default

    function init() {
        parent::init();

        $this->actionLeft = new \atk4\ui\Button(['icon'=> 'minus']);
        $this->action = new \atk4\ui\Button(['icon'=> 'plus']);

        $this->actionLeft->js('click', $this->jsInput()->val(new \atk4\ui\jsExpression('parseInt([])-1', [$this->jsInput()->val()])));
        $this->action->js('click', $this->jsInput()->val(new \atk4\ui\jsExpression('parseInt([])+1', [$this->jsInput()->val()])));
    }
}

// Add 3 counters
$seg->add(new Counter());
$seg->add(new Counter('40'));
$seg->add(new Counter('-20'));

// Add button to reload all counters
$bar = $layout->add('Buttons');
$b = $bar->add(['Button', 'Reload counter'])->js('click', new \atk4\ui\jsReload($seg));
