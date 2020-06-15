<?php

declare(strict_types=1);

namespace atk4\ui\demo;

require_once __DIR__ . '/../atk-init.php';

$layout = new \atk4\ui\Layout\Generic(['defaultTemplate' => __DIR__ . '/../templates/layout1.html']);

\atk4\ui\Lister::addTo($layout, [], ['Report'])
    ->setModel(new SomeData());

echo $layout->render();
