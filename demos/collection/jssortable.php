<?php

require_once __DIR__ . '/../atk-init.php';

$view = \atk4\ui\View::addTo($app, ['template' => new \atk4\ui\Template(
    '<div class="ui header">Click and drag country to reorder</div>
    <div id="{$_id}" style="cursor: pointer">
        <ul>
            {List}<li class="ui icon label" data-name="{$name}"><i class="{iso}ae{/} flag"></i> {name}andorra{/}</li>{/}
        </ul>
    </div>'
)]);

$lister = \atk4\ui\Lister::addTo($view, [], ['List']);
$lister->onHook('beforeRow', function ($l) {
    $l->current_row['iso'] = strtolower($l->current_row['iso']);
});
$lister->setModel(new Country($db))
    ->setLimit(20);

$sortable = \atk4\ui\jsSortable::addTo($view, ['container' => 'ul', 'draggable' => 'li', 'dataLabel' => 'name']);

$sortable->onReorder(function ($order, $src, $pos, $oldPos) {
    if (@$_GET['btn']) {
        return new \atk4\ui\jsToast(implode(' - ', $order));
    } else {
        return new \atk4\ui\jsToast($src . ' moved from position ' . $oldPos . ' to ' . $pos);
    }
});

$button = \atk4\ui\Button::addTo($app)->set('Get countries order');
$button->js('click', $sortable->jsGetOrders(['btn' => '1']));

//////////////////////////////////////////////////////////////////////////////////////////
\atk4\ui\View::addTo($app, ['ui' => 'divider']);
\atk4\ui\Header::addTo($app, ['Add Drag n drop to Grid']);

$g = \atk4\ui\Grid::addTo($app, ['paginator' => false]);
$g->setModel((new Country($db))->setLimit(6));

$dragHandler = $g->addDragHandler();
$dragHandler->onReorder(function ($order) {
    return new \atk4\ui\jsToast('New order: ' . implode(' - ', $order));
});
