<?php

declare(strict_types=1);

namespace atk4\ui\demo;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

\atk4\ui\Button::addTo($app, ['Dynamic scroll in Table', 'small right floated basic blue', 'iconRight' => 'right arrow'])
    ->link(['scroll-table']);
\atk4\ui\View::addTo($app, ['ui' => 'ui clearing divider']);

\atk4\ui\Header::addTo($app, ['Dynamic scroll in Lister']);

$container = \atk4\ui\View::addTo($app);

$view = \atk4\ui\View::addTo($container, ['template' => new \atk4\ui\Template('
{List}<div class="ui segment" style="height: 60px"><i class="{iso}ae{/} flag"></i> {name}andorra{/}</div>{/}
{$Content}')]);

$lister = \atk4\ui\Lister::addTo($view, [], ['List']);
$lister->onHook(\atk4\ui\Lister::HOOK_BEFORE_ROW, function (\atk4\ui\Lister $lister) {
    $lister->current_row->set('iso', mb_strtolower($lister->current_row->get('iso')));
});

$model = $lister->setModel(new Country($app->db));
//$model->addCondition('name','like','A%');

// add dynamic scrolling.
$lister->addJsPaginator(30, [], $container);
