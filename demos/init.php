<?php

date_default_timezone_set('UTC');

require '../vendor/autoload.php';

$app = new \atk4\ui\App('Agile UI v1.1 - Demo Suite', ['icon'=>'user']);

$app->initLayout($app->stickyGET('layout') ?: 'Admin');

$layout = $app->layout;

if (isset($layout->leftMenu)) {
    $layout->leftMenu->addItem(['Welcome Page', 'icon'=>'gift'], ['index']);
    $layout->leftMenu->addItem(['Layouts', 'icon'=>'object group'], ['layouts']);

    $form = $layout->leftMenu->addGroup(['Form', 'icon'=>'edit']);
    $form->addItem('Basics and Layouting', ['form']);
    $form->addItem('Input Field Decoration', ['field']);
    $form->addItem('Data Integration', ['form2']);
    $form->addItem('Form Multi-column layout', ['form3']);

    $form = $layout->leftMenu->addGroup(['Grid and Table', 'icon'=>'table']);
    $form->addItem('Data table with formatted columns', ['table']);
    $form->addItem('Table interractions', ['multitable']);
    $form->addItem('Grid - Table+Bar+Search+Paginator', ['grid']);
    $form->addItem('Interactivity - Modals and Expanders', ['expander']);
    $form->addItem('CRUD - Full editing solution', ['crud']);

    $basic = $layout->leftMenu->addGroup(['Basics', 'icon'=>'cubes']);
    $basic->addItem('View', ['view']);
    $basic->addItem('Button', ['button']);
    $basic->addItem('Header', ['header']);
    $basic->addItem('Message', ['message']);
    $basic->addItem('Labels', ['label']);
    $basic->addItem('Menu', ['menu']);
    $basic->addItem('Paginator', ['paginator']);

    $basic = $layout->leftMenu->addGroup(['Interactivity', 'icon'=>'talk']);
    $basic->addItem('JavaScript Events', ['button2']);
    $basic->addItem('Element Reloading', ['reloading']);
    $basic->addItem('Modal Dialogs', ['modal']);
    $basic->addItem('Sticky GET', ['sticky']);
    $basic->addItem('Recursive Views', ['recursive']);
    //$basic->addItem('Virtual Page', ['virtual']);

    $f = basename($_SERVER['PHP_SELF']);

    //$url = 'https://github.com/atk4/ui/blob/feature/grid-part2/demos/';
    $url = 'https://github.com/atk4/ui/blob/develop/demos/';

    // Would be nice if this would be a link.
    $layout->menu->addItem()->add(new \atk4\ui\Button(['View Source', 'teal', 'icon'=>'github']))
        ->setAttr('target', '_blank')->on('click', new \atk4\ui\jsExpression('document.location=[];', [$url.$f]));

    $img = 'https://github.com/atk4/ui/raw/07208a0af84109f0d6e3553e242720d8aeedb784/public/logo.png';
}

require_once 'somedatadef.php';
