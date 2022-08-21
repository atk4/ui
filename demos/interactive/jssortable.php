<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Button;
use Atk4\Ui\Header;
use Atk4\Ui\HtmlTemplate;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$view = \Atk4\Ui\View::addTo($app, ['template' => new HtmlTemplate(
    '<div class="ui header">Click and drag country to reorder</div>
    <div id="{$_id}" style="cursor: pointer">
        <ul>
            {List}<li class="ui icon label" data-name="{$atk_fp_country__name}"><i class="{$atk_fp_country__iso} flag"></i> {$atk_fp_country__name}</li>{/}
        </ul>
    </div>'
)]);

$lister = \Atk4\Ui\Lister::addTo($view, [], ['List']);
$lister->onHook(\Atk4\Ui\Lister::HOOK_BEFORE_ROW, function (\Atk4\Ui\Lister $lister) {
    $row = Country::assertInstanceOf($lister->currentRow);
    $row->iso = mb_strtolower($row->iso);
});
$model = new Country($app->db);
$model->setLimit(20);
$lister->setModel($model);

$sortable = \Atk4\Ui\JsSortable::addTo($view, ['container' => 'ul', 'draggable' => 'li', 'dataLabel' => 'name']);

$sortable->onReorder(function ($order, $src, $pos, $oldPos) {
    if ($_GET['btn'] ?? null) {
        return new \Atk4\Ui\JsToast(implode(' - ', $order));
    }

    return new \Atk4\Ui\JsToast($src . ' moved from position ' . $oldPos . ' to ' . $pos);
});

$button = Button::addTo($app)->set('Get countries order');
$button->js('click', $sortable->jsGetOrders(['btn' => '1']));

// -----------------------------------------------------------------------------

\Atk4\Ui\View::addTo($app, ['ui' => 'divider']);
Header::addTo($app, ['Add Drag n drop to Grid']);

$grid = \Atk4\Ui\Grid::addTo($app, ['paginator' => false]);
$grid->setModel((new Country($app->db))->setLimit(6));

$dragHandler = $grid->addDragHandler();
$dragHandler->onReorder(function ($order) {
    return new \Atk4\Ui\JsToast('New order: ' . implode(' - ', $order));
});
