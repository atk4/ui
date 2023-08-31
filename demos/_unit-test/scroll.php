<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Grid;
use Atk4\Ui\Js\JsExpression;
use Atk4\Ui\Js\JsToast;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$model = new Country($app->db);
$grid = Grid::addTo($app);
$grid->setModel($model);

$makeClickJsToastFx = static function (string $source) use ($grid) {
    return new JsToast(['message' => new JsExpression('[] + [] + []', [$source, ' clicked: ', $grid->jsRow()->data('id')])]);
};

$grid->addActionButton(['icon' => 'bell'], $makeClickJsToastFx('action'));
$grid->table->onRowClick($makeClickJsToastFx('row'));

// TODO JsPaginator should be possible to be added no later than setModel call
// https://github.com/atk4/ui/issues/1934
$grid->addJsPaginator(30);
