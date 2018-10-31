<?php

require 'init.php';
require 'database.php';

$container = $app->add('View');

$v = $container->add(['View', 'template' => new \atk4\ui\Template('
<div class="ui header">Top countries (alphabetically)</div>
{List}<div class="ui segment" style="height: 60px"><i class="{iso}ae{/} flag"></i> {name}andorra{/}</div>{/}
{$Content}</div>')]);

$l = $v->add('Lister', 'List')->addHook('beforeRow', function ($l) {
    $l->current_row['iso'] = strtolower($l->current_row['iso']);
});

$m = $l->setModel(new Country($db));

// add dynamic scrolling.
$l->addJsPaginator(20, $container);
