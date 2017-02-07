<?php

include 'init.php';

$t = $layout->add(['View', 'red'=>true,  'ui'=>'segment'])->add('Text');

$t->addParagraph('Layouts can be used to wrap your UI elements into HTML / Boilerplate');

$layout->add('Button')->set(['HTML without layout'])->link(['nolayout']);
$layout->add('Button')->set(['Manual layout'])->link(['layout']);
$layout->add('Button')->set(['Centered Layout'])->link(['header', 'layout'=>'centered']);
$layout->add('Button')->set(['Admin Layout'])->link(['layout2']);
$layout->add('Button')->set(['Exception Error'])->link(['error']);
