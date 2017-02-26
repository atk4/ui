<?php
/**
 * Demonstrates how to use interractive buttons.
 */
include 'init.php';

use \atk4\ui\Button;
use \atk4\ui\Buttons;
use \atk4\ui\Header;

$layout->js(true, new \atk4\ui\jsExpression('$.fn.api.settings.successTest = function(response) {
  if(response && response.eval) {
     var result = function(){ eval(response.eval); }.call(this.obj);
  }
  return false;
}'));

$layout->add(new Header(['Basic Button', 'size'=>2]));

// This button hides on page load
$b = $layout->add(new Button(['id'=>'b1']))->set('Hidden Button');
$b->js(true)->hide();

// This button hides when clicked
$b = $layout->add(new Button(['id'=>'b2']))->set('Hide on click Button');
$b->js('click')->hide();

$layout->add(new Header(['Callbacks', 'size'=>2]));

// On button click reload it and change it's title
$b = $layout->add(new Button(['id'=>'b3']))->set('Callback Test');
$b->on('click', function ($b) {
    return $b->text(rand(1, 20));
});
