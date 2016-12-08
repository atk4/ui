<?php
/**
 * Demonstrates how to use layouts
 */

require'../vendor/autoload.php';
    use \atk4\ui\Button;
    use \atk4\ui\Buttons;
    use \atk4\ui\Label;
    use \atk4\ui\Icon;
    use \atk4\ui\View;
    use \atk4\ui\Template;




try {

    $layout = new \atk4\ui\Layout\App(['template'=>'./templates/layout2.html']);


$view = new View(['template'=>new Template('Hello, {$tag1}, my name is {$tag2}')]);

$view->add(new Button('World'), 'tag1');
$view->add(new Button(['Agile UI', 'blue']), 'tag2');




$layout->add($view);


    echo $layout->render();

}catch(\atk4\core\Exception $e){ 
    var_Dump($e->getMessage());

    var_Dump($e->getParams());
    throw $e;
}

