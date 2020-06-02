<?php

namespace atk4\ui\demo;

require_once __DIR__ . '/../atk-init.php';

\atk4\ui\Button::addTo($app, ['Dynamic scroll in Table', 'small right floated basic blue', 'iconRight' => 'right arrow'])
    ->link(['scroll-table']);
\atk4\ui\View::addTo($app, ['ui' => 'ui clearing divider']);

\atk4\ui\Header::addTo($app, ['Dynamic scroll in Lister']);

$container = \atk4\ui\View::addTo($app);

$v = \atk4\ui\View::addTo($container, ['template' => new \atk4\ui\Template('
{List}<div class="ui segment" style="height: 60px"><i class="{iso}ae{/} flag"></i> {name}andorra{/}</div>{/}
{$Content}')]);

$l = \atk4\ui\Lister::addTo($v, [], ['List']);
$l->onHook(\atk4\ui\Lister::HOOK_BEFORE_ROW, function (atk4\ui\Lister $lister) {
    $lister->current_row->set('iso', mb_strtolower($lister->current_row->get('iso')));
});

$m = $l->setModel(new Country($db));
//$m->addCondition('name','like','A%');

// add dynamic scrolling.
$l->addJsPaginator(30, [], $container);
