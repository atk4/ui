<?php
/**
 * Demonstrates how to use interractive buttons
 */

require'../vendor/autoload.php';
use \atk4\ui\Button;
use \atk4\ui\Buttons;
use \atk4\ui\Label;
use \atk4\ui\Icon;
use \atk4\ui\View;
use \atk4\ui\Template;
use \atk4\ui\H2;




try {

    $layout = new \atk4\ui\Layout\App(['template'=>'./templates/layout2.html']);


    $layout->add(new H2('Basic Button'));

    $b = $layout->add(new Button(['id'=>'b1']))->set('Hidden Button');
    $b->js(true)->hide();

    $b = $layout->add(new Button(['id'=>'b2']))->set('Hide on click Button');
    $b->js('click')->hide();


    echo $layout->render();

}catch(\atk4\core\Exception $e){ 
    var_Dump($e->getMessage());

    var_Dump($e->getParams());
    throw $e;
}

