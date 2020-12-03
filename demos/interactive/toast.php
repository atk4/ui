<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

\Atk4\Ui\Header::addTo($app, ['Toast']);

$btn = \Atk4\Ui\Button::addTo($app)->set('Minimal');

$btn->on('click', new \Atk4\Ui\JsToast('Hi there!'));

$btn = \Atk4\Ui\Button::addTo($app)->set('Using a title');

$btn->on('click', new \Atk4\Ui\JsToast([
    'title' => 'Title',
    'message' => 'See I have a title',
]));

\Atk4\Ui\Header::addTo($app, ['Using class name']);

$btn = \Atk4\Ui\Button::addTo($app)->set('Success');
$btn->on('click', new \Atk4\Ui\JsToast([
    'title' => 'Success',
    'message' => 'Well done',
    'class' => 'success',
]));

$btn = \Atk4\Ui\Button::addTo($app)->set('Error');
$btn->on('click', new \Atk4\Ui\JsToast([
    'title' => 'Error',
    'message' => 'An error occured',
    'class' => 'error',
]));

$btn = \Atk4\Ui\Button::addTo($app)->set('Warning');
$btn->on('click', new \Atk4\Ui\JsToast([
    'title' => 'Warning',
    'message' => 'Behind you!',
    'class' => 'warning',
]));

\Atk4\Ui\Header::addTo($app, ['Using different position']);

$btn = \Atk4\Ui\Button::addTo($app)->set('Bottom Right');
$btn->on('click', new \Atk4\Ui\JsToast([
    'title' => 'Bottom Right',
    'message' => 'Should appear at the bottom on your right',
    'position' => 'bottom right',
]));

$btn = \Atk4\Ui\Button::addTo($app)->set('Top Center');
$btn->on('click', new \Atk4\Ui\JsToast([
    'title' => 'Top Center',
    'message' => 'Should appear at the top center',
    'position' => 'top center',
]));

\Atk4\Ui\Header::addTo($app, ['Other Options']);

$btn = \Atk4\Ui\Button::addTo($app)->set('5 seconds');
$btn->on('click', new \Atk4\Ui\JsToast([
    'title' => 'Timeout',
    'message' => 'I will stay here for 5 sec.',
    'displayTime' => 5000,
]));

$btn = \Atk4\Ui\Button::addTo($app)->set('For ever');
$btn->on('click', new \Atk4\Ui\JsToast([
    'title' => 'No Timeout',
    'message' => 'I will stay until you click me',
    'displayTime' => 0,
]));

$btn = \Atk4\Ui\Button::addTo($app)->set('Using Message style');
$btn->on('click', new \Atk4\Ui\JsToast([
    'title' => 'Awesome',
    'message' => 'I got my style from the message class',
    'class' => 'purple',
    'className' => ['toast' => 'ui message', 'title' => 'ui header'],
]));

$btn = \Atk4\Ui\Button::addTo($app)->set('With progress bar');
$btn->on('click', new \Atk4\Ui\JsToast([
    'title' => 'Awesome',
    'message' => 'See how long I will last',
    'showProgress' => 'bottom',
]));
