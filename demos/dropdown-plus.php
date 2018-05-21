<?php

require 'init.php';

$form = $app->add('Form');

$form->addField('enum',
                ['DropDown',
                    'caption' => 'Using Single Values',
                    'values'  => ['default', 'option1', 'option2', 'option3'],
                ]);

$form->addField('values',
                ['DropDown',
                    'caption' => 'Using values with default text',
                    'empty'   => 'Choose an option',
                    'values'  => ['default' => 'Default', 'option1' => 'Option 1', 'option2' => 'Option 2', 'option3' => 'Option 3'],
                ]);

$form->addField('icon',
                ['DropDown',
                    'caption' => 'Using icon',
                    'empty'   => 'Choose an icon',
                    'values'  => ['tag' => ['Tag', 'icon' => 'tag icon'], 'globe' => ['Globe', 'icon' => 'globe icon'], 'registered' => ['Registered', 'icon' => 'registered icon'], 'file' => ['File', 'icon' => 'file icon']],
                ]);

$form->addField('multi',
                ['DropDown',
                    'caption'    => 'Multiple selection',
                    'empty'      => 'Choose has many options needed',
                    'isMultiple' => true,
                    'values'     => ['default' => 'Default', 'option1' => 'Option 1', 'option2' => 'Option 2'],
                ]);

$form->onSubmit(function ($form) {
    $echo = print_r($form->model['enum'], true).' / ';
    $echo .= print_r($form->model['values'], true).' / ';
    $echo .= print_r($form->model['icon'], true).' / ';
    $echo .= print_r($form->model['multi'], true);

    echo $echo;
});

//////////////////////////////////////////////////////////
