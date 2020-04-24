<?php

chdir('..');
require_once 'init.php';

use atk4\ui\Button;
use atk4\ui\Buttons;
use atk4\ui\Header;

/**
 * Demonstrates how to use interractive buttons.
 */
Header::addTo($app, ['Basic Button']);

// This button hides on page load
$b = Button::addTo($app, ['Hidden Button']);
$b->js(true)->hide();

// This button hides when clicked
$b = Button::addTo($app, ['id' => 'b2'])->set('Hide on click Button');
$b->js('click')->hide();

Button::addTo($app, ['Redirect'])->on('click', $app->jsRedirect(['foo'=>'bar']));

if (isset($_GET['foo']) && $_GET['foo'] == 'bar') {
    $app->redirect(['foo'=>'baz']);
}

Header::addTo($app, ['js() method']);

$b = Button::addTo($app, ['Hide button B']);
$b2 = Button::addTo($app, ['B']);
$b->js('click', $b2->js()->hide('b2'))->hide('b1');

Header::addTo($app, ['on() method']);

$b = Button::addTo($app, ['Hide button C']);
$b2 = Button::addTo($app, ['C']);
$b->on('click', $b2->js()->hide('c2'))->hide('c1');

Header::addTo($app, ['Callbacks']);

// On button click reload it and change it's title
$b = Button::addTo($app, ['Callback Test']);
$b->on('click', function ($b) {
    return $b->text(rand(1, 20));
});

$b = Button::addTo($app, ['success']);
$b->on('click', function ($b) {
    return 'success';
});

$b = Button::addTo($app, ['failure']);
$b->on('click', function ($b) {
    throw new \atk4\data\ValidationException(['Everything is bad']);
});

Header::addTo($app, ['Callbacks on HTML element', 'subHeader' => 'Click on label below.']);

$label = \atk4\ui\Label::addTo($layout, ['Test']);

$label->on('click', function ($j, $arg1) {
    return 'width is ' . $arg1;
}, [new \atk4\ui\jsExpression('$(window).width()')]);
