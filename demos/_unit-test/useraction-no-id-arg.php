<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\App;
use Atk4\Ui\Button;
use Atk4\Ui\View;

/** @var App $app */
require_once __DIR__ . '/../init-app.php';

$country = new Country($app->db);
DemoActionsUtil::setupDemoActions($country);

$buttons = View::addTo($app, ['ui' => 'vertical basic buttons']);

foreach ($country->getUserActions() as $action) {
    $b = Button::addTo($buttons, [$action->getCaption()]);
    $b->on('click', $action); // action is intentionally not bound to entity nor ID arg is passed to executor
}
