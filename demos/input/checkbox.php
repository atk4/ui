<?php

require_once __DIR__ . '/../atk-init.php';

use atk4\ui\FormField\CheckBox;
use atk4\ui\View;

/**
 * Testing fields.
 */

\atk4\ui\Header::addTo($app, ['CheckBoxes', 'size'=>2]);

    CheckBox::addTo($app, ['Make my profile visible']);
    CheckBox::addTo($app, ['Make my profile visible ticked'])->set(true);

    View::addTo($app, ['ui' => 'divider']);
    CheckBox::addTo($app, ['Accept terms and conditions', 'slider']);

    View::addTo($app, ['ui' => 'divider']);
    CheckBox::addTo($app, ['Subscribe to weekly newsletter', 'toggle']);
    View::addTo($app, ['ui' => 'divider']);
    CheckBox::addTo($app, ['Look for the clues', 'disabled toggle'])->set(true);

    View::addTo($app, ['ui' => 'divider']);
    CheckBox::addTo($app, ['Custom setting?'])->js(true)->checkbox('set indeterminate');

    \atk4\ui\Header::addTo($app, ['CheckBoxes in a form', 'size'=>2]);
$form = \atk4\ui\Form::addTo($app);
$form->addField('test', ['CheckBox']);
$form->addField('test_checked', ['CheckBox'])->set(true);
$form->addField('also_checked', 'Hello World', 'boolean')->set(true);

    View::addTo($app, ['ui' => 'divider']);
    $c = new CheckBox('Selected checkbox by default');
    $c->set(true);
    $app->add($c);
