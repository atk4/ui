<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\HtmlTemplate;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

\Atk4\Ui\Button::addTo($app, ['Dynamic scroll in Table', 'small left floated basic blue', 'icon' => 'left arrow'])
    ->link(['scroll-table']);
\Atk4\Ui\Button::addTo($app, ['Dynamic scroll in Grid', 'small right floated basic blue', 'iconRight' => 'right arrow'])
    ->link(['scroll-grid']);
\Atk4\Ui\View::addTo($app, ['ui' => 'ui clearing divider']);

\Atk4\Ui\Header::addTo($app, ['Dynamic scroll in Container']);

$view = \Atk4\Ui\View::addTo($app)->addClass('ui basic segment atk-scroller');

$scrollContainer = \Atk4\Ui\View::addTo($view)->addClass('ui segment')->addStyle(['max-height' => '400px', 'overflow-y' => 'scroll']);

$listerTemplate = '<div id="{$_id}">{List}<div id="{$_id}" class="ui segment" style="height: 60px"><i class="{iso}ae{/} flag"></i> {name}andorra{/}</div>{/}{$Content}</div>';

$listerContainer = \Atk4\Ui\View::addTo($scrollContainer, ['template' => new HtmlTemplate($listerTemplate)]);

$lister = \Atk4\Ui\Lister::addTo($listerContainer, [], ['List']);
$lister->onHook(\Atk4\Ui\Lister::HOOK_BEFORE_ROW, function (\Atk4\Ui\Lister $lister) {
    $lister->current_row->set('iso', mb_strtolower($lister->current_row->get('iso')));
});
$lister->setModel(new Country($app->db));

// add dynamic scrolling.
$lister->addJsPaginator(20, ['stateContext' => '.atk-scroller'], $scrollContainer);
