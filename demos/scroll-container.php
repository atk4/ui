<?php

require 'init.php';
require 'database.php';

$app->add(['Button', 'Dynamic scroll in Table', 'small left floated basic blue', 'icon' => 'left arrow'])
    ->link(['scroll-table']);
$app->add(['Button', 'Dynamic scroll in Grid', 'small right floated basic blue', 'iconRight' => 'right arrow'])
    ->link(['scroll-grid']);
$app->add(['View', 'ui' => 'ui clearing divider']);

$app->add(['Header', 'Dynamic scroll in Container']);

$scroll_container = $app->add('View')->addClass('ui segment')->addStyle(['max-height' => '400px', 'overflow-y' => 'scroll']);

$lister_template = '<div id="{$_id}">{List}<div id="{$_id}" class="ui segment" style="height: 60px"><i class="{iso}ae{/} flag"></i> {name}andorra{/}</div>{/}{$Content}</div>';

$lister_container = $scroll_container->add(['View', 'template' => new \atk4\ui\Template($lister_template)]);

$l = $lister_container->add('Lister', 'List')->addHook('beforeRow', function ($l) {
    $l->current_row['iso'] = strtolower($l->current_row['iso']);
});
$l->setModel(new Country($db));

//add dynamic scrolling.
$l->addJsPaginator(20, [], $scroll_container);
