<?php

declare(strict_types=1);

namespace atk4\ui\demo;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

\atk4\ui\Button::addTo($app, ['Dynamic scroll in Table', 'small left floated basic blue', 'icon' => 'left arrow'])
    ->link(['scroll-table']);
\atk4\ui\Button::addTo($app, ['Dynamic scroll in Grid', 'small right floated basic blue', 'iconRight' => 'right arrow'])
    ->link(['scroll-grid']);
\atk4\ui\View::addTo($app, ['ui' => 'ui clearing divider']);

\atk4\ui\Header::addTo($app, ['Dynamic scroll in Container']);

$view = \atk4\ui\View::addTo($app)->addClass('ui basic segment atk-scroller');

$scrollContainer = \atk4\ui\View::addTo($view)->addClass('ui segment')->addStyle(['max-height' => '400px', 'overflow-y' => 'scroll']);

$listerTemplate = '<div id="{$_id}">{List}<div id="{$_id}" class="ui segment" style="height: 60px"><i class="{iso}ae{/} flag"></i> {name}andorra{/}</div>{/}{$Content}</div>';

$listerContainer = \atk4\ui\View::addTo($scrollContainer, ['template' => new \atk4\ui\Template($listerTemplate)]);

$lister = \atk4\ui\Lister::addTo($listerContainer, [], ['List']);
$lister->onHook(\atk4\ui\Lister::HOOK_BEFORE_ROW, function (\atk4\ui\Lister $lister) {
    $lister->current_row->set('iso', mb_strtolower($lister->current_row->get('iso')));
});
$lister->setModel(new Country($app->db));

// add dynamic scrolling.
$lister->addJsPaginator(20, ['stateContext' => '.atk-scroller'], $scrollContainer);
