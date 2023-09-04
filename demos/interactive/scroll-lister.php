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

Button::addTo($app, ['Dynamic scroll in Table', 'class.small right floated basic blue' => true, 'iconRight' => 'right arrow'])
    ->link(['scroll-table']);
View::addTo($app, ['ui' => 'clearing divider']);

Header::addTo($app, ['Dynamic scroll in Lister']);

$container = View::addTo($app);

$view = View::addTo($container, ['template' => new HtmlTemplate('
{List}<div class="ui segment" style="height: 60px;"><i class="{$atk_fp_country__iso} flag"></i> {$atk_fp_country__name}</div>{/}
{$Content}')]);

$lister = Lister::addTo($view, [], ['List']);
$lister->onHook(Lister::HOOK_BEFORE_ROW, static function (Lister $lister) {
    $row = Country::assertInstanceOf($lister->currentRow);
    $row->iso = mb_strtolower($row->iso);
});

$model = new Country($app->db);
$lister->setModel($model);

$lister->addJsPaginator(30, [], $container);
