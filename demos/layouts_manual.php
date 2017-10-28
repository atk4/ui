<?php
/**
 * Demonstrates how to use layouts.
 */
require '../vendor/autoload.php';
require 'somedatadef.php';
date_default_timezone_set('UTC');

$layout = new \atk4\ui\Layout\Generic(['defaultTemplate'=>'./templates/layout1.html']);

try {
    $layout->add(new \atk4\ui\Lister(), 'Report')
        ->setModel(new Somedata());

    echo $layout->render();
} catch (\atk4\core\Exception $e) {
    var_dump($e->getMessage());
    var_dump($e->getParams());

    throw $e;
}
