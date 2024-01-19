<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\App;
use Atk4\Ui\Button;
use Atk4\Ui\View;

/** @var App $app */
require_once __DIR__ . '/../init-app.php';

$country = new Country($app->db);
$entity = $country->loadAny();
$countryId = $entity->getId();

$view = View::addTo($app, ['style' => ['padding' => '50px']]);

DemoActionsUtil::setupDemoActions($country);

foreach (array_intersect_key($country->getUserActions(), ['add' => true, 'edit_argument_preview' => true]) as $action) {
    $b = Button::addTo($view, [$action->getCaption()]);
    // assign action to button using current model id as URL arguments
    $b->on('click', $action, ['args' => ['id' => $countryId]]);
}
