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

$inline_edit->onChange(function ($value) {
    $view = new \atk4\ui\Message();
    $view->init();
    $view->text->addParagraph('new value: '.$value);

    return $view;
});

$app->add(['ui' => 'divider']);

//****** ITEM SEARCH *****************************

$subHeader = 'Searching will reload the list of countries below with matching result.';
$app->add(['Header', 'Search using a Vue component', 'subHeader' => $subHeader]);

$m = new Country($db);

$lister_template = new atk4\ui\Template('<div id="{$_id}">{List}<div class="ui icon label"><i class="{$iso} flag"></i> {$name}</div>{/}</div>');

$view = $app->add('View');
$search = $view->add(['Component/ItemSearch', 'q' => $q, 'ui' => 'ui compact segment']);
$lister_container = $view->add(['View', 'template' => $lister_template]);
$lister = $lister_container->add('Lister', 'List')
            ->addHook('beforeRow', function ($l) {
                $l->current_row['iso'] = strtolower($l->current_row['iso']);
            });

$search->reload = $lister_container;
$lister->setModel($search->setModelCondition($m))->setLimit(100);
