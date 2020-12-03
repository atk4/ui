<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$layout = new \Atk4\Ui\Layout(['defaultTemplate' => __DIR__ . '/templates/layout1.html']);

\Atk4\Ui\Lister::addTo($layout, [], ['Report'])
    ->setModel(new SomeData());

$app->html = null;
$app->initLayout([\Atk4\Ui\Layout::class]);

\Atk4\Ui\Text::addTo($app->layout)->addHtml($layout->render());
