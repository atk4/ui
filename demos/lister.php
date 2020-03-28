<?php

date_default_timezone_set('UTC');
include_once __DIR__ . '/init.php';
include_once __DIR__ . '/database.php';

// default lister
\atk4\ui\Header::addTo($app)->set('Default lister');
\atk4\ui\Lister::addTo($app, ['defaultTemplate'=>'lister.html'])->setSource([
    ['icon'=>'map marker', 'title'=>'Krolewskie Jadlo', 'descr'=>'An excellent polish restaurant, quick delivery and hearty, filling meals'],
    ['icon'=> 'map marker', 'title'=>'Xian Famous Foods', 'descr'=>'A taste of Shaanxi\'s delicious culinary traditions, with delights like spicy cold noodles and lamb burgers.'],
    ['icon'=> 'check', 'title'=>'Sapporo Haru', 'descr'=>'Greenpoint\'s best choice for quick and delicious sushi'],
]);
\atk4\ui\View::addTo($app, ['ui' => 'clearing divider']);

// lister with custom template
$view = \atk4\ui\View::addTo($app, ['template' => new \atk4\ui\Template('<div>
<div class="ui header">Top 5 countries (alphabetically)</div>
{List}<div class="ui icon label"><i class="{$iso} flag"></i> {$name}</div>{/}
</div>')]);

$lister = \atk4\ui\Lister::addTo($view, [], ['List']);
$lister->onHook('beforeRow', function ($l) {
    $l->current_row['iso'] = strtolower($l->current_row['iso']);
});
$lister->setModel(new Country($db))
    ->setLimit(20);

\atk4\ui\View::addTo($app, ['ui' => 'clearing divider']);

// empty lister with default template
\atk4\ui\Header::addTo($app)->set('Empty default lister');
\atk4\ui\Lister::addTo($app, ['defaultTemplate'=>'lister.html'])->setSource([]);
\atk4\ui\View::addTo($app, ['ui' => 'clearing divider']);

// empty lister with custom template
$view = \atk4\ui\View::addTo($app, ['template' => new \atk4\ui\Template('<div>
<div class="ui header">Empty lister with custom template</div>
{List}<div class="ui icon label"><i class="{$iso} flag"></i> {$name}</div>{empty}no flags to show here{/}{/}
</div>')]);

$lister = \atk4\ui\Lister::addTo($view, [], ['List']);
$lister->onHook('beforeRow', function ($l) {
    $l->current_row['iso'] = strtolower($l->current_row['iso']);
});
$lister->setModel(new Country($db))
    ->addCondition('id', -1); // no such records so model will be empty
