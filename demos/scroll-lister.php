<?php

require_once __DIR__ . '/init.php';
require_once __DIR__ . '/database.php';

$app->add(['Button', 'Dynamic scroll in Table', 'small right floated basic blue', 'iconRight' => 'right arrow'])
    ->link(['scroll-table']);
$app->add(['View', 'ui' => 'ui clearing divider']);

$app->add(['Header', 'Dynamic scroll in Lister']);

$container = $app->add('View');

$v = $container->add(['View', 'template' => new \atk4\ui\Template('
{List}<div class="ui segment" style="height: 60px"><i class="{iso}ae{/} flag"></i> {name}andorra{/}</div>{/}
{$Content}')]);

$l = $v->add('Lister', 'List')->onHook('beforeRow', function ($l) {
    $l->current_row['iso'] = strtolower($l->current_row['iso']);
});

$m = $l->setModel(new Country($db));
//$m->addCondition('name','like','A%');

// add dynamic scrolling.
$l->addJsPaginator(30, [], $container);
