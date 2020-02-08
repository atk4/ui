<?php
/**
 * Testing fields.
 */
require_once __DIR__ . '/init.php';

use atk4\ui\FormField\CheckBox;
use atk4\ui\View;

$app->add(['Header', 'CheckBoxes', 'size'=>2]);

    $app->add(new CheckBox('Make my profile visible'));
    $app->add(new CheckBox('Make my profile visible ticked'))->set(true);

    $app->add(new View(['ui' => 'divider']));
    $app->add(['FormField/CheckBox', 'Accept terms and conditions', 'slider']);

    $app->add(new View(['ui' => 'divider']));
    $app->add(['FormField/CheckBox', 'Subscribe to weekly newsletter', 'toggle']);
    $app->add(new View(['ui' => 'divider']));
    $app->add(['FormField/CheckBox', 'Look for the clues', 'disabled toggle'])->set(true);

    $app->add(new View(['ui' => 'divider']));
    $app->add(['FormField/CheckBox', 'Custom setting?'])->js(true)->checkbox('set indeterminate');

    $app->add(['Header', 'CheckBoxes in a form', 'size'=>2]);
$form = $app->add('Form');
$form->addField('test', ['CheckBox']);
$form->addField('test_checked', ['CheckBox'])->set(true);
$form->addField('also_checked', 'Hello World', 'boolean')->set(true);

    $app->add(new View(['ui' => 'divider']));
    $c = new CheckBox('Selected checkbox by default');
    $c->set(true);
    $app->add($c);
