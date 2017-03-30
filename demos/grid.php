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
$m->addAction('What ID?', new \atk4\ui\jsExpression('alert($(this).closest("tr").data("id"))'));

$sel = $m->addSelection();
$m->menu->addItem('show selection')->on('click', new \atk4\ui\jsExpression('alert("Selected: "+[].join(", "))', [$sel->jsChecked()]));

$m->table->addColumn('iso', 'TableColumn/Link');


/*
$m = new File($db);
$m->addCondition('parent_folder_id', null);
$m->setOrder('is_folder desc, name');

$layout->add(['Header', 'MacOS File Finder', 'subHeader'=>'Component built around Table, Columns and jsReload']);

$vp = $layout->add('VirtualPage')->set(function ($vp) use ($m) {
    $m->action('delete')->execute();
    $m->importFromFilesystem(dirname(dirname(__FILE__)));
    $vp->add(['Button', 'Import Complete', 'big green fluid'])->link('multitable.php');
    $vp->js(true)->closest('.modal')->find('.header')->remove();
});

$layout->add(['Button', 'Re-Import From Filesystem', 'top attached'])->on('click', new \atk4\ui\jsModal('Now importing ... ', $vp));

$layout->add(new Finder('bottom attached'))
    ->addClass('top attached segment')
    ->setModel($m, ['SubFolder']);
 */
