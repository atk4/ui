<?php

chdir('..');
require_once 'init.php';
require_once 'database.php';

\atk4\ui\Button::addTo($app, ['Dynamic scroll in Table', 'small left floated basic blue', 'icon' => 'left arrow'])
    ->link(['scroll-table']);
\atk4\ui\Button::addTo($app, ['Dynamic scroll in Grid', 'small right floated basic blue', 'iconRight' => 'right arrow'])
    ->link(['scroll-grid']);
\atk4\ui\View::addTo($app, ['ui' => 'ui clearing divider']);

\atk4\ui\Header::addTo($app, ['Dynamic scroll in Container']);

$v = \atk4\ui\View::addTo($app)->addClass('ui basic segment atk-scroller');

$scroll_container = \atk4\ui\View::addTo($v)->addClass('ui segment')->addStyle(['max-height' => '400px', 'overflow-y' => 'scroll']);

$lister_template = '<div id="{$_id}">{List}<div id="{$_id}" class="ui segment" style="height: 60px"><i class="{iso}ae{/} flag"></i> {name}andorra{/}</div>{/}{$Content}</div>';

$lister_container = \atk4\ui\View::addTo($scroll_container, ['template' => new \atk4\ui\Template($lister_template)]);

$l = \atk4\ui\Lister::addTo($lister_container, [], ['List']);
$l->onHook('beforeRow', function ($l) {
    $l->current_row['iso'] = strtolower($l->current_row['iso']);
});
$l->setModel(new Country($db));

//add dynamic scrolling.
$l->addJsPaginator(20, ['stateContext' => '.atk-scroller'], $scroll_container);
