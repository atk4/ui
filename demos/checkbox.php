<?php
/**
 * Testing fields.
 */
require 'init.php';

    $app->add(['Header', 'CheckBoxes', 'size'=>2]);

    $app->add(new \atk4\ui\FormField\CheckBox('Make my profile visible'));
    $app->add(new \atk4\ui\FormField\CheckBox('Make my profile visible ticked'))->set(true);

    $app->add(new \atk4\ui\View(['ui' => 'divider']));
    $app->add(['FormField\CheckBox', 'Accept terms and conditions', 'slider']);

    $app->add(new \atk4\ui\View(['ui' => 'divider']));
    $app->add(['FormField\CheckBox', 'Subscribe to weekly newsletter', 'toggle']);
    $app->add(new \atk4\ui\View(['ui' => 'divider']));
    $app->add(['FormField\CheckBox', 'Look for the clues', 'disabled toggle'])->set(true);

    $app->add(new \atk4\ui\View(['ui' => 'divider']));
    $app->add(['FormField\CheckBox', 'Custom setting?'])->js(true)->checkbox('set indeterminate');

    $app->add(['Header', 'CheckBoxes in a form', 'size'=>2]);
$form = $app->add('Form');
$form->addField('test', ['CheckBox']);
$form->addField('test_checked', ['CheckBox'])->set(true);
$form->addField('also_checked', 'Hello World', 'boolean')->set(true);
