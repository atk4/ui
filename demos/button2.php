<?php
/**
 * Demonstrates how to use interractive buttons.
 */
require '../vendor/autoload.php';
use \atk4\ui\Button;
use \atk4\ui\Buttons;
use \atk4\ui\H2;
use \atk4\ui\Template;

try {
    $layout = new \atk4\ui\Layout\App(['template'=>'./templates/layout2.html']);

    $layout->add(new H2('Basic Button'));

    $b = $layout->add(new Button(['id'=>'b1']))->set('Hidden Button');
    $b->js(true)->hide();

    $b = $layout->add(new Button(['id'=>'b2']))->set('Hide on click Button');
    $b->js('click')->hide();

    echo $layout->render();
} catch (\atk4\core\Exception $e) {
    var_dump($e->getMessage());

    var_dump($e->getParams());
    throw $e;
}
