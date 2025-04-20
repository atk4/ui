<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\App;
use Atk4\Ui\Form;
use Atk4\Ui\Header;
use Atk4\Ui\Label;
use Atk4\Ui\Coluns;


/** @var App $app */
require_once __DIR__ . '/../init-app.php';

Header::addTo($app, ['Sliders', 'size' => 2]);

$form = Form::addTo($app);

$form->addControl(
    'slider_simple1',
    [
        Form\Control\Slider::class,
        'min' => 0,
        'max' => 10,
        'step' => 1,
        'start' => 5,
        'caption' => 'Simple Slider',
    ]
);

$slider2 = $form->addControl(
    'slider_simple2',
    [
        Form\Control\Slider::class,
        'labeled' => true,
        'ticked' => true,
        'min' => 0,
        'max' => 10,
        'step' => 1,
        'start' => 5,
        'caption' => 'Blue ticked and labeled simple slider',
    ]
);
$slider2->slider->addClass('blue');

$form->addControl(
    'slider_simple3',
    [
        Form\Control\Slider::class,
        'labeled' => true,
        'ticked' => true,
        'min' => 0,
        'max' => 10,
        'step' => 1,
        'start' => 5,
        'smooth' => true,
        'caption' => 'Smooth Blue ticked and labeled simple slider',
    ]
);

$form->addControl(
    'slider_ranged',
    [
        Form\Control\Slider::class,
        'labeled' => true,
        'ticked' => true,
        'min' => 0,
        'max' => 10,
        'step' => 1,
        'start' => 3,
        'end' => 6,
        'smooth' => true,
        'showThumbTooltip' => true,
        'tooltipConfig' => ['position' => 'top center', 'variation' => 'big blue'],
        'color' => 'blue',
        'caption' => 'Smooth Blue, ticked and labeled with tool-tips Ranged slider',
    ]
);

$form->addControl(
    'slider_custom',
    [
        Form\Control\Slider::class,
        'labeled' => true,
        'ticked' => true,
        'min' => 0,
        'max' => 6,
        'step' => 1,
        'start' => 2,
        'smooth' => true,
        'color' => 'green',
        'customLabels' => [
            'XS', 'S', 'M', 'L', 'XL', '2XL', '3XL',
        ],
        'caption' => 'Smooth Green, ticked and custom labeled slider',
    ]
);

$group = $form->addGroup(
    [
        'Vertical sliders',
        'inline' => true,
        'width' => 'six',
    ],
);

$g1 = $group->addControl(
    'slider_vertical',
    [
        Form\Control\Slider::class,
        'labeled' => true,
        'ticked' => true,
        'min' => 0,
        'max' => 6,
        'step' => 1,
        'start' => 2,
        'smooth' => true,
        'color' => 'red',
        'vertical' => true,
        'reversed' => true,
        'size' => 'small',
        'caption' => 'Smooth Red, ticked and vertical slider',
        'width' => 'one',
    ]
);

Label::addTo($g1, ['Smooth Red, ticked and vertical slider', 'class.right pointing basic label' => true], ['BeforeInput']);

$group->addControl(
    'slider_vertical_right',
    [
        Form\Control\Slider::class,
        'labeled' => true,
        'ticked' => true,
        'min' => 0,
        'max' => 6,
        'step' => 1,
        'start' => 2,
        'smooth' => true,
        'color' => 'yellow',
        'vertical' => true,
        'reversed' => true,
        'verticalHeight' => 300,
        'caption' => 'Smooth yellow, ticked, sized, custom height, vertical slider',
        'width' => 'two',
    ]
);

$group->addControl(
    'slider_vertical_right_ranged',
    [
        Form\Control\Slider::class,
        'labeled' => true,
        'ticked' => true,
        'min' => 0,
        'max' => 6,
        'step' => 1,
        'start' => 2,
        'end' => 6,
        'smooth' => true,
        'color' => 'green',
        'vertical' => true,
        'reversed' => true,
        'verticalHeight' => 400,
        'verticalRightAligned' => false,
        'size' => 'large',
        'caption' => 'Large',
        'width' => 'one',
    ]
);

$form->onSubmit(static function (Form $form) {
    print_r($form->entity->get());
});
