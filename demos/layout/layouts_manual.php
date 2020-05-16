<?php


namespace atk4\ui\demo;

/**
 * Demonstrates how to use layouts.
 */
chdir('..');
include_once '../vendor/autoload.php';
include_once '_includes/somedatadef.php';

date_default_timezone_set('UTC');

$layout = new \atk4\ui\Layout\Generic(['defaultTemplate' => './templates/layout1.html']);

try {
    \atk4\ui\Lister::addTo($layout, [], ['Report'])
        ->setModel(new Somedata());

    echo $layout->render();
} catch (\atk4\core\Exception $e) {
    var_dump($e->getMessage());
    var_dump($e->getParams());

    throw $e;
}
