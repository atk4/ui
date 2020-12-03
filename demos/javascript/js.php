<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Button;
use Atk4\Ui\Header;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

// Demonstrates how to use interractive buttons.
Header::addTo($app, ['Basic Button']);

// This button hides on page load
$b = Button::addTo($app, ['Hidden Button']);
$b->js(true)->hide();

// This button hides when clicked
$b = Button::addTo($app, ['id' => 'b2'])->set('Hide on click Button');
$b->js('click')->hide();

Button::addTo($app, ['Redirect'])->on('click', $app->jsRedirect(['foo' => 'bar']));

if (isset($_GET['foo']) && $_GET['foo'] === 'bar') {
    $app->redirect(['foo' => 'baz']);
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
    return $b->text(random_int(1, 20));
});

$b = Button::addTo($app, ['success']);
$b->on('click', function ($b) {
    return 'success';
});

$b = Button::addTo($app, ['failure']);
$b->on('click', function ($b) {
    throw new \Atk4\Data\ValidationException(['Everything is bad']);
});

Header::addTo($app, ['Callbacks on HTML element', 'subHeader' => 'Click on label below.']);

$label = \Atk4\Ui\Label::addTo($app->layout, ['Test']);

$label->on('click', function ($j, $arg1) {
    return 'width is ' . $arg1;
}, [new \Atk4\Ui\JsExpression('$(window).width()')]);
