<?php

require_once __DIR__ . '/init.php';
require_once __DIR__ . '/database.php';

$g = $app->add(['Grid']);
$g->setModel(new Country($db));
$g->ipp = 6;

$dragHandler = $g->addDragHandler();
$dragHandler->onReorder(function ($order) {
    return new \atk4\ui\jsNotify(implode(' - ', $order));
});

//////////////////////////////////////////////////////////////////////////////////////////

$view = $app->add(['View', 'template' => new \atk4\ui\Template('
    <div class="ui header">Click and drag country to reorder</div>
    <div id="{$_id}" style="cursor: pointer">
        <ul>
            {List}<li class="ui icon label" data-name="{$name}"><i class="{iso}ae{/} flag"></i> {name}andorra{/}</li>{/}
        </ul>
    </div>'
)]);

$view->add('Lister', 'List')
     ->addHook('beforeRow', function ($l) {
         $l->current_row['iso'] = strtolower($l->current_row['iso']);
     })->setModel(new Country($db))
     ->setLimit(20);

$sortable = $view->add(['jsSortable', 'container' => 'ul', 'draggable' => 'li', 'dataLabel' => 'name']);

$sortable->onReorder(function ($order, $src, $pos, $oldPos) {
    if (@$_GET['btn']) {
        return new \atk4\ui\jsNotify(implode(' - ', $order));
    } else {
        return new \atk4\ui\jsNotify($src.' moved from position '.$oldPos.' to '.$pos);
    }
});

$button = $app->add('Button')->set('Get countries order');
$button->js('click', $sortable->jsGetOrders(['btn' => '1']));
