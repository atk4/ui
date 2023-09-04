<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Header;
use Atk4\Ui\HtmlTemplate;
use Atk4\Ui\ItemsPerPageSelector;
use Atk4\Ui\Lister;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php'; // default lister

Header::addTo($app)->set('Default lister');
Lister::addTo($app, ['defaultTemplate' => 'lister.html'])->setSource([
    ['icon' => 'map marker', 'title' => 'Krolewskie Jadlo', 'descr' => 'An excellent polish restaurant, quick delivery and hearty, filling meals'],
    ['icon' => 'map marker', 'title' => 'Xian Famous Foods', 'descr' => 'A taste of Shaanxi\'s delicious culinary traditions, with delights like spicy cold noodles and lamb burgers.'],
    ['icon' => 'check', 'title' => 'Sapporo Haru', 'descr' => 'Greenpoint\'s best choice for quick and delicious sushi'],
]);
View::addTo($app, ['ui' => 'clearing divider']);

// lister with custom template
$view = View::addTo($app, ['template' => new HtmlTemplate('<div>
<div class="ui header">Top 20 countries (alphabetically)</div>
{List}<div class="ui icon label"><i class="{$atk_fp_country__iso} flag"></i> {$atk_fp_country__name}</div>{/}
</div>')]);

$lister = Lister::addTo($view, [], ['List']);
$lister->onHook(Lister::HOOK_BEFORE_ROW, static function (Lister $lister) {
    $row = Country::assertInstanceOf($lister->currentRow);
    $row->iso = mb_strtolower($row->iso);
});
$model = new Country($app->db);
$model->setLimit(20);
$lister->setModel($model);

View::addTo($app, ['ui' => 'clearing divider']);

// empty lister with default template
Header::addTo($app)->set('Empty default lister');
Lister::addTo($app, ['defaultTemplate' => 'lister.html'])->setSource([]);
View::addTo($app, ['ui' => 'clearing divider']);

// empty lister with custom template
$view = View::addTo($app, ['template' => new HtmlTemplate('<div>
<div class="ui header">Empty lister with custom template</div>
{List}<div class="ui icon label"><i class="{$atk_fp_country__iso} flag"></i> {$atk_fp_country__name}</div>{empty}no flags to show here{/}{/}
</div>')]);

$lister = Lister::addTo($view, [], ['List']);
$lister->onHook(Lister::HOOK_BEFORE_ROW, static function (Lister $lister) {
    $row = Country::assertInstanceOf($lister->currentRow);
    $row->iso = mb_strtolower($row->iso);
});
$model = new Country($app->db);
$model->addCondition(Country::hinting()->fieldName()->id, -1); // no such records so model will be empty
$lister->setModel($model);

View::addTo($app, ['ui' => 'clearing divider']);
Header::addTo($app, ['Item per page', 'subHeader' => 'Lister can display a certain amount of items']);

$container = View::addTo($app);

$view = View::addTo($container, ['template' => new HtmlTemplate('<div>
<ul>
{List}<li class="ui icon label"><i class="{$atk_fp_country__iso} flag"></i>{$atk_fp_country__name}</li>{/}
</ul>{$Content}</div>')]);

$lister = Lister::addTo($view, [], ['List']);
$lister->onHook(Lister::HOOK_BEFORE_ROW, static function (Lister $lister) {
    $row = Country::assertInstanceOf($lister->currentRow);
    $row->iso = mb_strtolower($row->iso);
});

$model = new Country($app->db);
$model->setLimit(12);
$lister->setModel($model);

$ipp = ItemsPerPageSelector::addTo($view, ['label' => 'Select how many countries:', 'pageLengthItems' => [12, 24, 36]], ['Content']);

$ipp->onPageLengthSelect(static function (int $ipp) use ($model, $container) {
    $model->setLimit($ipp);

    return $container;
});
