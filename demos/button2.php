<?php
/**
 * Demonstrates how to use interractive buttons.
 */
require '../vendor/autoload.php';
use \atk4\ui\Button;
use \atk4\ui\Buttons;
use \atk4\ui\H2;

try {
    $layout = new \atk4\ui\Layout\App(['defaultTemplate'=>'./templates/layout2.html']);

    $layout->js(true, new \atk4\ui\jsExpression('$.fn.api.settings.successTest = function(response) {
  if(response && response.eval) {
     var result = function(){ eval(response.eval); }.call(this.obj);
  }
  return false;
}'));

    $layout->add(new H2('Basic Button'));

    $b = $layout->add(new Button(['id'=>'b1']))->set('Hidden Button');
    $b->js(true)->hide();

    $b = $layout->add(new Button(['id'=>'b2']))->set('Hide on click Button');
    $b->js('click')->hide();

    $layout->add(new H2('Callbacks'));

    $b = $layout->add(new Button(['id'=>'b3']))->set('Callback Test');
    $b->on('click', function ($b) {
        return $b->text(rand(1, 20));
    });

    echo $layout->render();
} catch (\atk4\core\Exception $e) {
    var_dump($e->getMessage());

    var_dump($e->getParams());
    throw $e;
}
