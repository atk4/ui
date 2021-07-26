<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\HtmlTemplate;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

\Atk4\Ui\Button::addTo($app, ['Dynamic scroll in Table', 'small right floated basic blue', 'iconRight' => 'right arrow'])
    ->link(['scroll-table']);
\Atk4\Ui\View::addTo($app, ['ui' => 'ui clearing divider']);

\Atk4\Ui\Header::addTo($app, ['Dynamic scroll in Lister']);

$container = \Atk4\Ui\View::addTo($app);

$view = \Atk4\Ui\View::addTo($container, ['template' => new HtmlTemplate('
{List}<div class="ui segment" style="height: 60px"><i class="{$atk_fp_country__iso} flag"></i> {$atk_fp_country__name}</div>{/}
{$Content}')]);

$lister = \Atk4\Ui\Lister::addTo($view, [], ['List']);
$lister->onHook(\Atk4\Ui\Lister::HOOK_BEFORE_ROW, function (\Atk4\Ui\Lister $lister) {
    $row = Country::assertInstanceOf($lister->current_row);
    $row->iso = mb_strtolower($row->iso);
});

$model = $lister->setModel(new Country($app->db));
//$model->addCondition(Country::hinting()->fieldName()->name, 'like', 'A%');

// add dynamic scrolling.
$lister->addJsPaginator(30, [], $container);
