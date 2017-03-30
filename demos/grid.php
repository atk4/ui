<?php

require 'init.php';
require 'database.php';

$m = $layout->add(['Grid']);
$m->setModel(new Country($db));
$m->addQuickSearch();

$m->menu->addItem(['Add Country', 'icon'=>'add square'], new \atk4\ui\jsExpression('alert(123)'));
$m->menu->addItem(['Re-Import', 'icon'=>'power']);
$m->menu->addItem(['Delete All', 'icon'=>'trash', 'red active']);

$m->addAction('Say HI', new \atk4\ui\jsExpression('alert("hi")'));
$m->addAction(['icon'=>'pencil'], new \atk4\ui\jsExpression('alert($(this).closest("tr").data("id"))'));

$sel = $m->addSelection();
$m->menu->addItem('show selection')->on('click', new \atk4\ui\jsExpression(
    'alert("Selected: "+[].join(", "))', [$sel->jsChecked()]
));
