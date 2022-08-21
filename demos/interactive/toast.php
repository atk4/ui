<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Button;
use Atk4\Ui\Header;
use Atk4\Ui\JsToast;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

Header::addTo($app, ['Toast']);

$btn = Button::addTo($app)->set('Minimal');

$btn->on('click', new JsToast('Hi there!'));

$btn = Button::addTo($app)->set('Using a title');

$btn->on('click', new JsToast([
    'title' => 'Title',
    'message' => 'See I have a title',
]));

Header::addTo($app, ['Using class name']);

$btn = Button::addTo($app)->set('Success');
$btn->on('click', new JsToast([
    'title' => 'Success',
    'message' => 'Well done',
    'class' => 'success',
]));

$btn = Button::addTo($app)->set('Error');
$btn->on('click', new JsToast([
    'title' => 'Error',
    'message' => 'An error occured',
    'class' => 'error',
]));

$btn = Button::addTo($app)->set('Warning');
$btn->on('click', new JsToast([
    'title' => 'Warning',
    'message' => 'Behind you!',
    'class' => 'warning',
]));

Header::addTo($app, ['Using different position']);

$btn = Button::addTo($app)->set('Bottom Right');
$btn->on('click', new JsToast([
    'title' => 'Bottom Right',
    'message' => 'Should appear at the bottom on your right',
    'position' => 'bottom right',
]));

$btn = Button::addTo($app)->set('Top Center');
$btn->on('click', new JsToast([
    'title' => 'Top Center',
    'message' => 'Should appear at the top center',
    'position' => 'top center',
]));

Header::addTo($app, ['Other Options']);

$btn = Button::addTo($app)->set('5 seconds');
$btn->on('click', new JsToast([
    'title' => 'Timeout',
    'message' => 'I will stay here for 5 sec.',
    'displayTime' => 5000,
]));

$btn = Button::addTo($app)->set('For ever');
$btn->on('click', new JsToast([
    'title' => 'No Timeout',
    'message' => 'I will stay until you click me',
    'displayTime' => 0,
]));

$btn = Button::addTo($app)->set('Using Message style');
$btn->on('click', new JsToast([
    'title' => 'Awesome',
    'message' => 'I got my style from the message class',
    'class' => 'purple',
    'className' => ['toast' => 'ui message', 'title' => 'ui header'],
]));

$btn = Button::addTo($app)->set('With progress bar');
$btn->on('click', new JsToast([
    'title' => 'Awesome',
    'message' => 'See how long I will last',
    'showProgress' => 'bottom',
]));
