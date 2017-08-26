<?php

require 'init.php';

$img = 'https://cdn.rawgit.com/atk4/ui/07208a0af84109f0d6e3553e242720d8aeedb784/public/logo.png';

$seg = $app->layout->add(['View', 'ui'=>'segment']);
$seg->add(['Header', 'H1 Header', 'size'=>1]);
$seg->add(['Header', 'H2 Header', 'size'=>2]);
$seg->add(['Header', 'H3 Header', 'size'=>3]);
$seg->add(['Header', 'H4 Header', 'size'=>4]);
$seg->add(['Header', 'H5 Header', 'size'=>5, 'dividing']);
$seg->add(['View', 'element'=>'P'])->set('This is a following paragraph of text');

$seg->add(['Header', 'H1', 'size'=>1, 'subHeader'=>'H1 subheader']);
$seg->add(['Header', 'H5', 'size'=>5, 'subHeader'=>'H5 subheader']);

$seg = $app->layout->add(['View', 'ui'=>'segment']);
$seg->add(['Header', 'Huge Header', 'size'=>'huge']);
$seg->add(['Header', 'Large Header', 'size'=>'large']);
$seg->add(['Header', 'Medium Header', 'size'=>'medium']);
$seg->add(['Header', 'Small Header', 'size'=>'small']);
$seg->add(['Header', 'Tiny Header', 'size'=>'tiny']);

$seg->add(['Header', 'Sub Header', 'sub']);

$seg = $app->layout->add(['View', 'ui'=>'segment']);
$seg->add(['Header', 'Header with icon', 'icon'=>'settings']);
$seg->add(['Header', 'Header with icon', 'icon'=>'settings', 'subHeader'=>'and with sub-header']);
$seg->add(['Header', 'Header with image', 'image'=>$img, 'subHeader'=>'and with sub-header']);

$seg = $app->layout->add(['View', 'ui'=>'segment']);
$seg->add(['Header', 'Center-aligned', 'aligned'=>'center', 'icon'=>'settings', 'subHeader'=>'header with icon']);

$seg = $app->layout->add(['View', 'ui'=>'segment']);
$seg->add(['Header', 'Center-aligned', 'aligned'=>'center', 'icon'=>'circular users', 'subHeader'=>'header with icon']);

$seg = $app->layout->add(['View', 'ui'=>'segment']);
$seg->add(['Header', 'Center-aligned', 'aligned'=>'center', 'image'=>$img, 'subHeader'=>'header with image']);

$seg = $app->layout->add(['View', 'ui'=>'segment']);
$seg->add(['Header', 'Center-aligned', 'aligned'=>'center', 'image'=>[$img, 'disabled'], 'subHeader'=>'header with image']);
