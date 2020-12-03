<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\HtmlTemplate;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php'; // default lister

\Atk4\Ui\Header::addTo($app)->set('Default lister');
\Atk4\Ui\Lister::addTo($app, ['defaultTemplate' => 'lister.html'])->setSource([
    ['icon' => 'map marker', 'title' => 'Krolewskie Jadlo', 'descr' => 'An excellent polish restaurant, quick delivery and hearty, filling meals'],
    ['icon' => 'map marker', 'title' => 'Xian Famous Foods', 'descr' => 'A taste of Shaanxi\'s delicious culinary traditions, with delights like spicy cold noodles and lamb burgers.'],
    ['icon' => 'check', 'title' => 'Sapporo Haru', 'descr' => 'Greenpoint\'s best choice for quick and delicious sushi'],
]);
\Atk4\Ui\View::addTo($app, ['ui' => 'clearing divider']);

// lister with custom template
$view = \Atk4\Ui\View::addTo($app, ['template' => new HtmlTemplate('<div>
<div class="ui header">Top 20 countries (alphabetically)</div>
{List}<div class="ui icon label"><i class="{$iso} flag"></i> {$name}</div>{/}
</div>')]);

$lister = \Atk4\Ui\Lister::addTo($view, [], ['List']);
$lister->onHook(\Atk4\Ui\Lister::HOOK_BEFORE_ROW, function (\Atk4\Ui\Lister $lister) {
    $lister->current_row->set('iso', mb_strtolower($lister->current_row->get('iso')));
});
$lister->setModel(new Country($app->db))
    ->setLimit(20);

\Atk4\Ui\View::addTo($app, ['ui' => 'clearing divider']);

// empty lister with default template
\Atk4\Ui\Header::addTo($app)->set('Empty default lister');
\Atk4\Ui\Lister::addTo($app, ['defaultTemplate' => 'lister.html'])->setSource([]);
\Atk4\Ui\View::addTo($app, ['ui' => 'clearing divider']);

// empty lister with custom template
$view = \Atk4\Ui\View::addTo($app, ['template' => new HtmlTemplate('<div>
<div class="ui header">Empty lister with custom template</div>
{List}<div class="ui icon label"><i class="{$iso} flag"></i> {$name}</div>{empty}no flags to show here{/}{/}
</div>')]);

$lister = \Atk4\Ui\Lister::addTo($view, [], ['List']);
$lister->onHook(\Atk4\Ui\Lister::HOOK_BEFORE_ROW, function (\Atk4\Ui\Lister $lister) {
    $lister->current_row->set('iso', mb_strtolower($lister->current_row->get('iso')));
});
$lister->setModel(new Country($app->db))
    ->addCondition('id', -1); // no such records so model will be empty

\Atk4\Ui\View::addTo($app, ['ui' => 'clearing divider']);
\Atk4\Ui\Header::addTo($app, ['Item per page', 'subHeader' => 'Lister can display a certain amount of items']);

$container = \Atk4\Ui\View::addTo($app);

$view = \Atk4\Ui\View::addTo($container, ['template' => new HtmlTemplate('<div>
<ul>
{List}<li class="ui icon label"><i class="{iso}ae{/} flag"></i> {name}andorra{/}</li>{/}
</ul>{$Content}</div>')]);

$lister = \Atk4\Ui\Lister::addTo($view, [], ['List']);
$lister->onHook(\Atk4\Ui\Lister::HOOK_BEFORE_ROW, function (\Atk4\Ui\Lister $lister) {
    $lister->current_row->set('iso', mb_strtolower($lister->current_row->get('iso')));
});

$model = $lister->setModel(new Country($app->db))->setLimit(12);

$ipp = \Atk4\Ui\ItemsPerPageSelector::addTo($view, ['label' => 'Select how many countries:', 'pageLengthItems' => [12, 24, 36]], ['Content']);

$ipp->onPageLengthSelect(function ($ipp) use ($model, $container) {
    $model->setLimit($ipp);

    return $container;
});
