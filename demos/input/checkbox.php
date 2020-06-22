<?php

declare(strict_types=1);

namespace atk4\ui\demo;

use atk4\ui\Form;
use atk4\ui\View;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

// Testing fields.

\atk4\ui\Header::addTo($app, ['CheckBoxes', 'size' => 2]);

Form\Field\Checkbox::addTo($app, ['Make my profile visible']);
Form\Field\Checkbox::addTo($app, ['Make my profile visible ticked'])->set(true);

View::addTo($app, ['ui' => 'divider']);
Form\Field\Checkbox::addTo($app, ['Accept terms and conditions', 'slider']);

View::addTo($app, ['ui' => 'divider']);
Form\Field\Checkbox::addTo($app, ['Subscribe to weekly newsletter', 'toggle']);
View::addTo($app, ['ui' => 'divider']);
Form\Field\Checkbox::addTo($app, ['Look for the clues', 'disabled toggle'])->set(true);

View::addTo($app, ['ui' => 'divider']);
Form\Field\Checkbox::addTo($app, ['Custom setting?'])->js(true)->checkbox('set indeterminate');

\atk4\ui\Header::addTo($app, ['CheckBoxes in a form', 'size' => 2]);
$form = Form::addTo($app);
$form->addField('test', [Form\Field\Checkbox::class]);
$form->addField('test_checked', [Form\Field\Checkbox::class])->set(true);
$form->addField('also_checked', 'Hello World', 'boolean')->set(true);

View::addTo($app, ['ui' => 'divider']);
$c = new Form\Field\Checkbox('Selected checkbox by default');
$c->set(true);
$app->add($c);
