<?php

require 'init.php';
require 'database.php';

$app->add(['Header', 'Component', 'size' => 2, 'icon' => 'vuejs', 'subHeader' => 'UI view handle by Vue.js']);
$app->add(['ui' => 'divider']);

//****** Inline Edit *****************************

$m = new Country($db);
$m->loadAny();

$subHeader = 'Try me. I will restore value on "Escape" or save it on "Enter" or when field get blur after it has been changed.';
$app->add(['Header', 'Inline editing.', 'size' => 3, 'subHeader' => $subHeader]);

$inline_edit = $app->add(['Component/InlineEdit']);
$inline_edit->setModel($m);

$inline_edit->onChange(function ($id, $value) {
    return new \atk4\ui\jsToast([
          'title'       => 'Saving',
          'message'     => 'Country : { '.$id.' : '.$value.' }',
          'class'       => 'success',
          'displayTime' => 5000,
    ]);
});

$app->add(['ui' => 'divider']);

//****** ITEM SEARCH *****************************

$subHeader = 'Searching will reload the list of countries below with matching result.';
$app->add(['Header', 'Search using a Vue component', 'subHeader' => $subHeader]);

$m = new Country($db);

//Search query will be set in _q
$q = $_GET['_q'] ? $_GET['_q'] : null;
if ($q) {
    $m->addCondition('name', 'like', '%'.$q.'%');
}

$lister_template = new atk4\ui\Template('<div id="{$_id}">{List}<div class="ui icon label"><i class="{$iso} flag"></i> {$name}</div>{/}</div>');

$view = $app->add('View');
$search = $view->add(['Component/ItemSearch', 'q' => $q, 'ui' => 'ui compact segment']);
$lister_container = $view->add(['View', 'template' => $lister_template]);
$lister = $lister_container->add('Lister', 'List')
            ->addHook('beforeRow', function ($l) {
                $l->current_row['iso'] = strtolower($l->current_row['iso']);
            });

$lister->setModel($m)->setLimit(100);
$search->reload = $lister_container;
