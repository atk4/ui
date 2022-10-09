<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Button;
use Atk4\Ui\Exception;
use Atk4\Ui\Header;
use Atk4\Ui\Jquery;
use Atk4\Ui\JsExpression;
use Atk4\Ui\Label;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

// Demonstrates how to use interractive buttons.
Header::addTo($app, ['Basic Button']);

// This button hides on page load
$b = Button::addTo($app, ['Hidden Button']);
$b->js(true)->hide();

// This button hides when clicked
$b = Button::addTo($app, ['name' => 'b2'])->set('Hide on click Button');
$b->js('click')->hide();

Button::addTo($app, ['Redirect'])->on('click', null, $app->jsRedirect(['foo' => 'bar']));

if (isset($_GET['foo']) && $_GET['foo'] === 'bar') {
    $app->redirect(['foo' => 'baz']);
}

Header::addTo($app, ['js() method']);

$b = Button::addTo($app, ['Hide button B']);
$b2 = Button::addTo($app, ['B']);
$b->js('click', $b2->js()->hide())->addClass('disabled')->addClass('disabled');

Header::addTo($app, ['on() method']);

$b = Button::addTo($app, ['Hide button C and self']);
$b2 = Button::addTo($app, ['C']);
$b->on('click', null, $b2->js()->hide())->hide();

Header::addTo($app, ['Callbacks']);

// On button click reload it and change it's title
$b = Button::addTo($app, ['Callback Test']);
$b->on('click', null, function (Jquery $j) {
    return $j->text(random_int(1, 20));
});

$b = Button::addTo($app, ['success']);
$b->on('click', null, function (Jquery $j) {
    return 'success';
});

$b = Button::addTo($app, ['failure']);
$b->on('click', null, function (Jquery $j) {
    throw new Exception('Everything is bad');
});

Header::addTo($app, ['Callbacks on HTML element', 'subHeader' => 'Click on label below.']);

$label = Label::addTo($app->layout, ['Test']);

$label->on('click', null, function (Jquery $j, $arg1) {
    return 'width is ' . $arg1;
}, [new JsExpression('$(window).width()')]);
