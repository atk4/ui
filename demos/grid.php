<?php

require 'init.php';
require 'database.php';

$g = $layout->add(['Grid']);
$g->setModel(new Country($db));
$g->addQuickSearch();

$g->menu->addItem(['Add Country', 'icon'=>'add square'], new \atk4\ui\jsExpression('alert(123)'));
$g->menu->addItem(['Re-Import', 'icon'=>'power'], new \atk4\ui\jsReload($g));
$g->menu->addItem(['Delete All', 'icon'=>'trash', 'red active']);

$g->addAction('Say HI', new \atk4\ui\jsExpression('alert("hi")'));
$g->addAction(['icon'=>'pencil'], new \atk4\ui\jsExpression('alert($(this).closest("tr").data("id"))'));

$g->addColumn(new atk4\ui\TableColumn\Delete());

$sel = $g->addSelection();
$g->menu->addItem('show selection')->on('click', new \atk4\ui\jsExpression(
    'alert("Selected: "+[])', [$sel->jsChecked()]
));

$g->ipp = 10;
