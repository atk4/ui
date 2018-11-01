<?php

date_default_timezone_set('UTC');
include 'init.php';
include 'database.php';

$app->add(['Lister', 'defaultTemplate'=>'lister.html'])->setSource([
    ['icon'=>'map marker', 'title'=>'Krolewskie Jadlo', 'descr'=>'An excellent polish restaurant, quick delivery and hearty, filling meals'],
    ['icon'=> 'map marker', 'title'=>'Xian Famous Foods', 'descr'=>'A taste of Shaanxi\'s delicious culinary traditions, with delights like spicy cold noodles and lamb burgers.'],
    ['icon'=> 'check', 'title'=>'Sapporo Haru', 'descr'=>'Greenpoint\'s best choice for quick and delicious sushi'],
]);

$view = $app->add(['View', 'template' => new \atk4\ui\Template('<div>
<div class="ui header">Top 5 countries (alphabetically)</div>
{List}<div class="ui icon label"><i class="{iso}ae{/} flag"></i> {name}andorra{/}</div>{/}
</div>')]);

$view->add('Lister', 'List')
    ->addHook('beforeRow', function ($l) {
        $l->current_row['iso'] = strtolower($l->current_row['iso']);
    })
    ->setModel(new Country($db))
    ->setLimit(20);
