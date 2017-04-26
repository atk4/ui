<?php

include 'init.php';

// buttons configuration: [page, title]
$buttons = [
    ['page' => 'layouts_nolayout',                      'title' => 'HTML without layout'],
    ['page' => ['table', 'layout'=>'centered'],         'title' => 'Centered frame'],
    ['page' => ['header', 'layout'=>'centeredpage'],    'title' => 'Centered page'],
    ['page' => 'layouts_manual',                        'title' => 'Manual layout'],
    ['page' => 'layouts_admin',                         'title' => 'Admin Layout'],
    ['page' => 'layouts_error',                         'title' => 'Exception Error'],
];

// layout
$layout->add(['View', 'red'=>true,  'ui'=>'segment'])
    ->add('Text')
    ->addParagraph('Layouts can be used to wrap your UI elements into HTML / Boilerplate');

// toolbar
$tb = $layout->add('View');

// iframe
$i = $layout->add(['View', 'green'=>true, 'ui'=>'segment'])->setElement('iframe')->setStyle(['width'=>'100%', 'height'=>'500px']);

// create menu
$m = $tb->add(['Menu', 'pointing']);
$m->addHeader('Choose layout:');
foreach ($buttons as $k=>$args) {
    $m->addItem($args['title'], $i->js()->attr('src', $layout->app->url($args['page'])));
}
