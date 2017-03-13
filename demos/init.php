<?php

require '../vendor/autoload.php';

$app = new \atk4\ui\App('Agile UI v0.4 - Demo Suite', ['icon'=>'user']);

$app->initLayout((isset($_GET['layout']) && $_GET['layout'] === 'centered') ? 'Centered' : 'Admin');

$layout = $app->layout;

if (isset($layout->leftMenu)) {
    $layout->leftMenu->addItem(['Welcome Page', 'icon'=>'gift'], ['index']);
    $layout->leftMenu->addItem(['Layouts', 'icon'=>'object group'], ['layouts']);

    $form = $layout->leftMenu->addGroup(['Form', 'icon'=>'edit']);
    $form->addItem('Basics and Layouting', ['form']);
    $form->addItem('Input Field Decoration', ['field']);
    $form->addItem('Data Integration', ['form2']);

    $form = $layout->leftMenu->addGroup(['Grid and Table', 'icon'=>'table']);
    $form->addItem('Data table with formatted columns', ['table']);
    $form->addItem('Table interractions', ['multitable']);
    $form->addItem('Grid - Table+Bar+Search+Paginator', ['table']);
    $form->addItem('Interactivity - Modals and Expanders', ['expander']);

    $basic = $layout->leftMenu->addGroup(['Basics', 'icon'=>'cubes']);
    $basic->addItem('Button', ['button']);
    $basic->addItem('Header', ['header']);
    $basic->addItem('Columns', ['columns']);

    $basic = $layout->leftMenu->addGroup(['Interactivity', 'icon'=>'talk']);
    $basic->addItem('JavaScript Events', ['button2']);

    $f = basename($_SERVER['PHP_SELF']);

    // Would be nice if this would be a link.
    $layout->menu->addItem()->add(new \atk4\ui\Button(['View Source', 'teal', 'icon'=>'github']))
        ->setAttr('target', '_blan')->on('click', new \atk4\ui\jsExpression('document.location=[];', ['https://github.com/atk4/ui/blob/develop/demos/'.$f]));

    $img = 'https://github.com/atk4/ui/raw/07208a0af84109f0d6e3553e242720d8aeedb784/public/logo.png';
}

require_once 'somedatadef.php';
