<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Button;
use Atk4\Ui\Header;
use Atk4\Ui\Js\JsToast;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

Header::addTo($app, ['Toast']);

$button = Button::addTo($app)->set('Minimal');
$button->on('click', new JsToast('Hi there!'));

$button = Button::addTo($app)->set('Using a title');
$button->on('click', new JsToast([
    'title' => 'Title',
    'message' => 'See I have a title',
]));

Header::addTo($app, ['Using class name']);

$button = Button::addTo($app)->set('Success');
$button->on('click', new JsToast([
    'title' => 'Success',
    'message' => 'Well done',
    'class' => 'success',
]));

$button = Button::addTo($app)->set('Error');
$button->on('click', new JsToast([
    'title' => 'Error',
    'message' => 'An error occurred',
    'class' => 'error',
]));

$button = Button::addTo($app)->set('Warning');
$button->on('click', new JsToast([
    'title' => 'Warning',
    'message' => 'Behind you!',
    'class' => 'warning',
]));

Header::addTo($app, ['Using different position']);

$button = Button::addTo($app)->set('Bottom Right');
$button->on('click', new JsToast([
    'title' => 'Bottom Right',
    'message' => 'Should appear at the bottom on your right',
    'position' => 'bottom right',
]));

$button = Button::addTo($app)->set('Top Center');
$button->on('click', new JsToast([
    'title' => 'Top Center',
    'message' => 'Should appear at the top center',
    'position' => 'top center',
]));

Header::addTo($app, ['Other Options']);

$button = Button::addTo($app)->set('5 seconds');
$button->on('click', new JsToast([
    'title' => 'Timeout',
    'message' => 'I will stay here for 5 sec.',
    'displayTime' => 5000,
]));

$button = Button::addTo($app)->set('For ever');
$button->on('click', new JsToast([
    'title' => 'No Timeout',
    'message' => 'I will stay until you click me',
    'displayTime' => 0,
]));

$button = Button::addTo($app)->set('Using Message style');
$button->on('click', new JsToast([
    'title' => 'Awesome',
    'message' => 'I got my style from the message class',
    'class' => 'purple',
    'className' => ['toast' => 'ui message', 'title' => 'header cust'],
]));

$button = Button::addTo($app)->set('With progress bar');
$button->on('click', new JsToast([
    'title' => 'Awesome',
    'message' => 'See how long I will last',
    'showProgress' => 'bottom',
]));
