<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Button;
use Atk4\Ui\Header;
use Atk4\Ui\HtmlTemplate;
use Atk4\Ui\Lister;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

Button::addTo($app, ['Dynamic scroll in Table', 'class.small left floated basic blue' => true, 'icon' => 'left arrow'])
    ->link(['scroll-table']);
Button::addTo($app, ['Dynamic scroll in Grid', 'class.small right floated basic blue' => true, 'iconRight' => 'right arrow'])
    ->link(['scroll-grid']);
View::addTo($app, ['ui' => 'clearing divider']);

Header::addTo($app, ['Dynamic scroll in Container']);

$view = View::addTo($app)->addClass('ui basic segment atk-scroller');

$scrollContainer = View::addTo($view)->addClass('ui segment')->setStyle(['max-height' => '400px', 'overflow-y' => 'scroll']);

$listerTemplate = '<div {$attributes}>{List}<div id="{$_id}" class="ui segment" style="height: 60px;"><i class="{$'
    . Country::hinting()->fieldName()->iso . '} flag"></i> {$'
    . Country::hinting()->fieldName()->name . '}</div>{/}{$Content}</div>';

$listerContainer = View::addTo($scrollContainer, ['template' => new HtmlTemplate($listerTemplate)]);

$lister = Lister::addTo($listerContainer, [], ['List']);
$lister->onHook(Lister::HOOK_BEFORE_ROW, static function (Lister $lister) {
    $row = Country::assertInstanceOf($lister->currentRow);
    $row->iso = mb_strtolower($row->iso);
});
$lister->setModel(new Country($app->db));

$lister->addJsPaginator(20, ['stateContext' => '.atk-scroller'], $scrollContainer);
