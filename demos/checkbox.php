<?php
/**
 * Testing fields.
 */
require 'init.php';

    $app->add(new \atk4\ui\Header(['CheckBoxes', 'size' => 2]));

    $app->add(new \atk4\ui\FormField\CheckBox('Make my profile visible'));

    $app->add(new \atk4\ui\View(['ui' => 'divider']));
    $app->add(new \atk4\ui\FormField\CheckBox(['Accept terms and conditions', 'slider']));

    $app->add(new \atk4\ui\View(['ui' => 'divider']));
    $app->add(new \atk4\ui\FormField\CheckBox(['Subscribe to weekly newsletter', 'toggle']));

    $app->add(new \atk4\ui\View(['ui' => 'divider']));
    $app->add(new \atk4\ui\FormField\CheckBox(['Custom setting?']))->js(true)->checkbox('set indeterminate');

    $app->add(new \atk4\ui\View(['ui' => 'divider']));
    $c = new \atk4\ui\FormField\CheckBox(['Selected checkbox by default']);
    $c->set(true);
    $app->add($c);
