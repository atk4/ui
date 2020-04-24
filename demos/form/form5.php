<?php

chdir('..');

require_once 'atk-init.php';\atk4\ui\View::addTo($app, [
    'Forms below focus on Data integration and automated layouts',
    'ui' => 'ignored warning message',
]);

$cc = \atk4\ui\Columns::addTo($app);
$f = \atk4\ui\Form::addTo($cc->addColumn());

// adding field without model creates a regular line
$f->addField('one');

// Second argument string is used as a caption
$f->addField('two', 'Caption');

// Array second is a default seed for default line field
$f->addField('three', ['caption' => 'Caption2']);

// Use zeroth argument of the seed to specify standard class
$f->addField('four', ['CheckBox', 'caption' => 'Caption2']);

// Use explicit object for user-defined or 3rd party field
$f->addField('five', new \atk4\ui\FormField\CheckBox())->set(true);

// Objects still accept seed
$f->addField('six', new \atk4\ui\FormField\CheckBox(['caption' => 'Caption3']));

$a = [];
$m = new \atk4\data\Model(new \atk4\data\Persistence\Array_($a));

// model field uses regular line form field by default
$m->addField('one');

// caption is a top-level property of a field
$m->addField('two', ['caption' => 'Caption']);

// ui can also specify caption which is a form-specific
$m->addField('three', ['ui' => ['form' => ['caption' => 'Caption']]]);

// type is converted into CheckBox form field with caption as a seed
$m->addField('four', ['type' => 'boolean', 'ui' => ['form' => ['caption' => 'Caption2']]]);

// Can specify class for a checkbox explicitly
$m->addField('five', ['ui' => ['form' => ['CheckBox', 'caption' => 'Caption3']]]);

// Form-specific caption overrides general caption of a field. Also you can specify object instead of seed
$m->addField('six', ['caption' => 'badcaption', 'ui' => ['form' => new \atk4\ui\FormField\CheckBox(['caption' => 'Caption4'])]]);

$f = \atk4\ui\Form::addTo($cc->addColumn());
$f->setModel($m);

// Next form won't initalize default fields, but we'll add them individually
$f = \atk4\ui\Form::addTo($cc->addColumn());
$f->setModel($m, false);

// adding that same field but with custom form field seed
$f->addField('one', ['caption' => 'Caption0']);

// another way to override caption
$f->addField('two', 'Caption2');

// We can override type, but seed from model will still be respected
$f->addField('three', ['CheckBox']);

// We override type and caption here
$f->addField('four', ['Line', 'caption' => 'CaptionX']);

// We can specify form field object. It's still seeded with caption from model.
$f->addField('five', new \atk4\ui\FormField\CheckBox());

// can add field that does not exist in a model
$f->addField('nine', new \atk4\ui\FormField\CheckBox(['caption' => 'Caption3']));
