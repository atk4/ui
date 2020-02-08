<?php

date_default_timezone_set('UTC');
include_once __DIR__ . '/init.php';
include_once __DIR__ . '/database.php';

// default lister
$app->add('Header')->set('Default lister');
$app->add(['Lister', 'defaultTemplate'=>'lister.html'])->setSource([
    ['icon'=>'map marker', 'title'=>'Krolewskie Jadlo', 'descr'=>'An excellent polish restaurant, quick delivery and hearty, filling meals'],
    ['icon'=> 'map marker', 'title'=>'Xian Famous Foods', 'descr'=>'A taste of Shaanxi\'s delicious culinary traditions, with delights like spicy cold noodles and lamb burgers.'],
    ['icon'=> 'check', 'title'=>'Sapporo Haru', 'descr'=>'Greenpoint\'s best choice for quick and delicious sushi'],
]);
$app->add(['ui' => 'clearing divider']);

// lister with custom template
$view = $app->add(['View', 'template' => new \atk4\ui\Template('<div>
<div class="ui header">Top 5 countries (alphabetically)</div>
{List}<div class="ui icon label"><i class="{$iso} flag"></i> {$name}</div>{/}
</div>')]);

$view->add('Lister', 'List')
    ->addHook('beforeRow', function ($l) {
        $l->current_row['iso'] = strtolower($l->current_row['iso']);
    })
    ->setModel(new Country($db))
    ->setLimit(20);

$app->add(['ui' => 'clearing divider']);

// empty lister with default template
$app->add('Header')->set('Empty default lister');
$app->add(['Lister', 'defaultTemplate'=>'lister.html'])->setSource([]);
$app->add(['ui' => 'clearing divider']);

// empty lister with custom template
$view = $app->add(['View', 'template' => new \atk4\ui\Template('<div>
<div class="ui header">Empty lister with custom template</div>
{List}<div class="ui icon label"><i class="{$iso} flag"></i> {$name}</div>{empty}no flags to show here{/}{/}
</div>')]);

$view->add('Lister', 'List')
    ->addHook('beforeRow', function ($l) {
        $l->current_row['iso'] = strtolower($l->current_row['iso']);
    })
    ->setModel(new Country($db))
    ->addCondition('id', -1); // no such records so model will be empty
