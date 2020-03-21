<?php
/**
 * Testing fields.
 */
require_once __DIR__ . '/init.php';

use atk4\ui\FormField\CheckBox;
use atk4\ui\View;

\atk4\ui\Header::addTo($app, ['CheckBoxes', 'size'=>2]);

    $app->add(new CheckBox('Make my profile visible'));
    $app->add(new CheckBox('Make my profile visible ticked'))->set(true);

    $app->add(new View(['ui' => 'divider']));
    CheckBox::addTo($app, ['Accept terms and conditions', 'slider']);

    $app->add(new View(['ui' => 'divider']));
    CheckBox::addTo($app, ['Subscribe to weekly newsletter', 'toggle']);
    $app->add(new View(['ui' => 'divider']));
    CheckBox::addTo($app, ['Look for the clues', 'disabled toggle'])->set(true);

    $app->add(new View(['ui' => 'divider']));
    CheckBox::addTo($app, ['Custom setting?'])->js(true)->checkbox('set indeterminate');

    \atk4\ui\Header::addTo($app, ['CheckBoxes in a form', 'size'=>2]);
$form = \atk4\ui\Form::addTo($app);
$form->addField('test', ['CheckBox']);
$form->addField('test_checked', ['CheckBox'])->set(true);
$form->addField('also_checked', 'Hello World', 'boolean')->set(true);

    $app->add(new View(['ui' => 'divider']));
    $c = new CheckBox('Selected checkbox by default');
    $c->set(true);
    $app->add($c);
