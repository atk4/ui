<?php

declare(strict_types=1);

namespace atk4\ui\demo;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

$layout = new \atk4\ui\Layout(['defaultTemplate' => __DIR__ . '/templates/layout1.html']);

\atk4\ui\Lister::addTo($layout, [], ['Report'])
    ->setModel(new SomeData());

$app->html = null;
$app->initLayout([\atk4\ui\Layout::class]);

\atk4\ui\Text::addTo($app->layout)->addHtml($layout->render());
