<?php

require_once __DIR__ . '/init.php';
require_once __DIR__ . '/database.php';

$container = \atk4\ui\View::addTo($app);

$v = \atk4\ui\View::addTo($container, ['template' => new \atk4\ui\Template('<div>
<div class="ui header">Top countries (alphabetically)</div><ul>
{List}<li class="ui icon label"><i class="{iso}ae{/} flag"></i> {name}andorra{/}</li>{/}
</ul>{$Content}</div>')]);

$l = \atk4\ui\Lister::addTo($v, [], ['List'])->onHook('beforeRow', function ($l) {
    $l->current_row['iso'] = strtolower($l->current_row['iso']);
});

$m = $l->setModel(new Country($db))->setLimit(12);

$ipp = \atk4\ui\ItemsPerPageSelector::addTo($v, ['label' => 'Select how many countries:', 'pageLengthItems' => [12, 24, 36]], ['Content']);

$ipp->onPageLengthSelect(function ($ipp) use ($m, $container) {
    $m->setLimit($ipp);

    return $container;
});
