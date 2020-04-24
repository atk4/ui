<?php

chdir('..');
require_once 'init.php';

\atk4\ui\Button::addTo($app, ['Dynamic scroll in Table', 'small right floated basic blue', 'iconRight' => 'right arrow'])
    ->link(['scroll-table']);
\atk4\ui\View::addTo($app, ['ui' => 'ui clearing divider']);

\atk4\ui\Header::addTo($app, ['Dynamic scroll in Lister']);

$container = \atk4\ui\View::addTo($app);

$v = \atk4\ui\View::addTo($container, ['template' => new \atk4\ui\Template('
{List}<div class="ui segment" style="height: 60px"><i class="{iso}ae{/} flag"></i> {name}andorra{/}</div>{/}
{$Content}')]);

$l = \atk4\ui\Lister::addTo($v, [], ['List']);
$l->onHook('beforeRow', function ($l) {
    $l->current_row['iso'] = strtolower($l->current_row['iso']);
});

$m = $l->setModel(new Country($db));
//$m->addCondition('name','like','A%');

// add dynamic scrolling.
$l->addJsPaginator(30, [], $container);
