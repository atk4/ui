<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Button;
use Atk4\Ui\Grid;
use Atk4\Ui\Header;
use Atk4\Ui\HtmlTemplate;
use Atk4\Ui\JsSortable;
use Atk4\Ui\JsToast;
use Atk4\Ui\Lister;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$view = View::addTo($app, ['template' => new HtmlTemplate(
    '<div class="ui header">Click and drag country to reorder</div>
    <div id="{$_id}" style="cursor: pointer;">
        <ul>
            {List}<li class="ui icon label" data-name="{$atk_fp_country__name}"><i class="{$atk_fp_country__iso} flag"></i> {$atk_fp_country__name}</li>{/}
        </ul>
    </div>'
)]);

$lister = Lister::addTo($view, [], ['List']);
$lister->onHook(Lister::HOOK_BEFORE_ROW, function (Lister $lister) {
    $row = Country::assertInstanceOf($lister->currentRow);
    $row->iso = mb_strtolower($row->iso);
});
$model = new Country($app->db);
$model->setLimit(20);
$lister->setModel($model);

$sortable = JsSortable::addTo($view, ['container' => 'ul', 'draggable' => 'li', 'dataLabel' => 'name']);

$sortable->onReorder(function (array $order, string $src, int $pos, int $oldPos) {
    if ($_GET['btn'] ?? null) {
        return new JsToast(implode(' - ', $order));
    }

    return new JsToast($src . ' moved from position ' . $oldPos . ' to ' . $pos);
});

$button = Button::addTo($app)->set('Get countries order');
$button->js('click', $sortable->jsSendSortOrders(['btn' => '1']));

// -----------------------------------------------------------------------------

View::addTo($app, ['ui' => 'divider']);
Header::addTo($app, ['Add drag sorting to grid']);

$grid = Grid::addTo($app, ['paginator' => false]);
$grid->setModel((new Country($app->db))->setLimit(6));

$dragHandler = $grid->addDragHandler();
$dragHandler->onReorder(function (array $order) {
    return new JsToast('New order: ' . implode(' - ', $order));
});
