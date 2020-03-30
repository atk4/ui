<?php

require_once __DIR__ . '/init.php';

\atk4\ui\Header::addTo($app, ['Toast']);

$btn = \atk4\ui\Button::addTo($app)->set('Minimal');

$btn->on('click', new \atk4\ui\jsToast('Hi there!'));

$btn = \atk4\ui\Button::addTo($app)->set('Using a title');

$btn->on('click', new \atk4\ui\jsToast([
    'title'   => 'Title',
    'message' => 'See I have a title',
]));

\atk4\ui\Header::addTo($app, ['Using class name']);

$btn = \atk4\ui\Button::addTo($app)->set('Success');
$btn->on('click', new \atk4\ui\jsToast([
    'title'   => 'Success',
    'message' => 'Well done',
    'class'   => 'success',
]));

$btn = \atk4\ui\Button::addTo($app)->set('Error');
$btn->on('click', new \atk4\ui\jsToast([
    'title'   => 'Error',
    'message' => 'An error occur',
    'class'   => 'error',
]));

$btn = \atk4\ui\Button::addTo($app)->set('Warning');
$btn->on('click', new \atk4\ui\jsToast([
    'title'   => 'Warning',
    'message' => 'Behind you!',
    'class'   => 'warning',
]));

\atk4\ui\Header::addTo($app, ['Using different position']);

$btn = \atk4\ui\Button::addTo($app)->set('Bottom Right');
$btn->on('click', new \atk4\ui\jsToast([
    'title'    => 'Bottom Left',
    'message'  => 'Should appear at the bottom on your left',
    'position' => 'bottom right',
]));

$btn = \atk4\ui\Button::addTo($app)->set('Top Center');
$btn->on('click', new \atk4\ui\jsToast([
    'title'    => 'Bottom Left',
    'message'  => 'Should appear at the bottom on your left',
    'position' => 'top center',
]));

\atk4\ui\Header::addTo($app, ['Other Options']);

$btn = \atk4\ui\Button::addTo($app)->set('5 seconds');
$btn->on('click', new \atk4\ui\jsToast([
    'title'       => 'Bottom Left',
    'message'     => 'I will stay here for 5 sec.',
    'displayTime' => 5000,
]));

$btn = \atk4\ui\Button::addTo($app)->set('For ever');
$btn->on('click', new \atk4\ui\jsToast([
    'title'       => 'Bottom Left',
    'message'     => 'I will stay until you click me',
    'displayTime' => 0,
]));

$btn = \atk4\ui\Button::addTo($app)->set('Using Message style');
$btn->on('click', new \atk4\ui\jsToast([
    'title'       => 'Awesome',
    'message'     => 'I got my style from the message class',
    'class'       => 'purple',
    'className'   => ['toast' => 'ui message', 'title' => 'ui header'],
]));

$btn = \atk4\ui\Button::addTo($app)->set('With progress bar');
$btn->on('click', new \atk4\ui\jsToast([
    'title'        => 'Awesome',
    'message'      => 'See how long I will last',
    'showProgress' => 'bottom',
]));
