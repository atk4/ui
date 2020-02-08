<?php

require __DIR__ . '/init.php';

$app->add(['Header', 'Toast']);

$btn = $app->add('Button')->set('Minimal');

$btn->on('click', new \atk4\ui\jsToast('Hi there!'));

$btn = $app->add('Button')->set('Using a title');

$btn->on('click', new \atk4\ui\jsToast([
    'title'   => 'Title',
    'message' => 'See I have a title',
]));

$app->add(['Header', 'Using class name']);

$btn = $app->add('Button')->set('Success');
$btn->on('click', new \atk4\ui\jsToast([
    'title'   => 'Success',
    'message' => 'Well done',
    'class'   => 'success',
]));

$btn = $app->add('Button')->set('Error');
$btn->on('click', new \atk4\ui\jsToast([
    'title'   => 'Error',
    'message' => 'An error occur',
    'class'   => 'error',
]));

$btn = $app->add('Button')->set('Warning');
$btn->on('click', new \atk4\ui\jsToast([
    'title'   => 'Warning',
    'message' => 'Behind you!',
    'class'   => 'warning',
]));

$app->add(['Header', 'Using different position']);

$btn = $app->add('Button')->set('Bottom Right');
$btn->on('click', new \atk4\ui\jsToast([
    'title'    => 'Bottom Left',
    'message'  => 'Should appear at the bottom on your left',
    'position' => 'bottom right',
]));

$btn = $app->add('Button')->set('Top Center');
$btn->on('click', new \atk4\ui\jsToast([
    'title'    => 'Bottom Left',
    'message'  => 'Should appear at the bottom on your left',
    'position' => 'top center',
]));

$app->add(['Header', 'Other Options']);

$btn = $app->add('Button')->set('5 seconds');
$btn->on('click', new \atk4\ui\jsToast([
    'title'       => 'Bottom Left',
    'message'     => 'I will stay here for 5 sec.',
    'displayTime' => 5000,
]));

$btn = $app->add('Button')->set('For ever');
$btn->on('click', new \atk4\ui\jsToast([
    'title'       => 'Bottom Left',
    'message'     => 'I will stay until you click me',
    'displayTime' => 0,
]));

$btn = $app->add('Button')->set('Using Message style');
$btn->on('click', new \atk4\ui\jsToast([
    'title'       => 'Awesome',
    'message'     => 'I got my style from the message class',
    'class'       => 'purple',
    'className'   => ['toast' => 'ui message', 'title' => 'ui header'],
]));

$btn = $app->add('Button')->set('With progress bar');
$btn->on('click', new \atk4\ui\jsToast([
    'title'        => 'Awesome',
    'message'      => 'See how long I will last',
    'showProgress' => 'bottom',
]));
