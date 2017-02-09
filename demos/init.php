<?php

require '../vendor/autoload.php';

$app = new \atk4\ui\App('Agile UI - Demo Suite', ['icon'=>'user']);

$app->initLayout((isset($_GET['layout']) && $_GET['layout'] === 'centered') ? 'Centered' : 'Admin');

$layout = $app->layout;

if (!isset($layout->leftMenu)) {
    return;
}

$layout->leftMenu->addItem(['Welcome Page', 'icon'=>'gift'], ['index']);
$layout->leftMenu->addItem(['Layouts', 'icon'=>'object group'], ['layouts']);

$form = $layout->leftMenu->addGroup(['Form', 'icon'=>'edit']);
$form->addItem('Basics and Layouting', ['form']);
$form->addItem('Input Field Decoration', ['field']);
$form->addItem('Data Integration', ['form2']);

$form = $layout->leftMenu->addGroup(['Grid', 'icon'=>'table']);
$form->addItem('Column Formats and Features', ['grid']);

$basic = $layout->leftMenu->addGroup(['Basics', 'icon'=>'cubes']);
$basic->addItem('Button', ['button']);
$basic->addItem('Icon');
$basic->addItem('Label');
$basic->addItem('Header', ['header']);

$basic = $layout->leftMenu->addGroup(['Interactivity', 'icon'=>'talk']);
$basic->addItem('JavaScript Events', ['button2']);

$f = basename($_SERVER['PHP_SELF']);

// Would be nice if this would be a link.
$layout->menu->addItem()->add(new \atk4\ui\Button(['View Source', 'teal', 'icon'=>'github']))
    ->setAttr('target', '_blan')->on('click', new \atk4\ui\jsExpression('document.location=[];', ['https://github.com/atk4/ui/blob/develop/demos/'.$f]));

$img = 'https://github.com/atk4/ui/raw/07208a0af84109f0d6e3553e242720d8aeedb784/public/logo.png';


require'somedatadef.php';
