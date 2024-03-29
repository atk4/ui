<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\App;
use Atk4\Ui\Button;
use Atk4\Ui\Grid;
use Atk4\Ui\Header;
use Atk4\Ui\HtmlTemplate;
use Atk4\Ui\Js\JsToast;
use Atk4\Ui\JsSortable;
use Atk4\Ui\Lister;
use Atk4\Ui\View;

/** @var App $app */
require_once __DIR__ . '/../init-app.php';

$view = View::addTo($app, ['template' => new HtmlTemplate(
    '<div class="ui header">Click and drag country to reorder</div>
    <div style="cursor: pointer;" {$attributes}>
        <ul>
            {List}<li class="ui icon label" data-name="{$atk_fp_country__name}"><i class="{$atk_fp_country__iso} flag"></i> {$atk_fp_country__name}</li>{/}
        </ul>
    </div>'
)]);

$lister = Lister::addTo($view, [], ['List']);
$lister->onHook(Lister::HOOK_BEFORE_ROW, static function (Lister $lister) {
    $row = Country::assertInstanceOf($lister->currentRow);
    $row->iso = mb_strtolower($row->iso);
});
$model = new Country($app->db);
$model->setLimit(20);
$lister->setModel($model);

$sortable = JsSortable::addTo($view, ['container' => 'ul', 'draggable' => 'li', 'dataLabel' => 'name']);

$sortable->onReorder(static function (array $orderedNames, string $sourceName, int $pos, int $oldPos) use ($app) {
    if ($app->tryGetRequestQueryParam('btn')) {
        return new JsToast(implode(' - ', $orderedNames));
    }

    return new JsToast($sourceName . ' moved from position ' . $oldPos . ' to ' . $pos);
}, $model->getField($model->fieldName()->name));

$button = Button::addTo($app)->set('Get countries order');
$button->on('click', $sortable->jsSendSortOrders(['btn' => '1']));

// -----------------------------------------------------------------------------

View::addTo($app, ['ui' => 'divider']);
Header::addTo($app, ['Add drag sorting to grid']);

$grid = Grid::addTo($app, ['paginator' => false]);
$grid->setModel((new Country($app->db))->setLimit(6));

$dragHandler = $grid->addDragHandler();
$dragHandler->onReorder(static function (array $orderedIds) use ($grid) {
    return new JsToast('New order: ' . implode(' - ', array_map(static fn ($id) => $grid->getApp()->uiPersistence->typecastSaveField($grid->model->getIdField(), $id), $orderedIds)));
});
