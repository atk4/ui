<?php
/**
 * Demonstrates how to use interractive buttons.
 */
include 'init.php';

use \atk4\ui\Button;
use \atk4\ui\Buttons;
use \atk4\ui\Header;

$layout->add(new Header('Basic Button'));

// This button hides on page load
$b = $layout->add(new Button('Hidden Button'));
$b->js(true)->hide();

// This button hides when clicked
$b = $layout->add(new Button(['id'=>'b2']))->set('Hide on click Button');
$b->js('click')->hide();

$layout->add(new Header('js() method'));

$b = $layout->add(new Button('Hide button B'));
$b2 = $layout->add(new Button('B'));
$b->js('click', $b2->js()->hide('b2'))->hide('b1');

$layout->add(new Header('on() method'));

$b = $layout->add(new Button('Hide button C'));
$b2 = $layout->add(new Button('C'));
$b->on('click', $b2->js()->hide('c2'))->hide('c1');

$layout->add(new Header('Callbacks'));

// On button click reload it and change it's title
$b = $layout->add(new Button('Callback Test'));
$b->on('click', function ($b) {
    return $b->text(rand(1, 20));
});

$b = $layout->add(new Button('success'));
$b->on('click', function ($b) {
    return 'success';
});

$b = $layout->add(new Button('failure'));
$b->on('click', function ($b) {
    throw new \atk4\data\ValidationException(['Everything is bad']);
});
