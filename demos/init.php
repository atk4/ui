<?php

date_default_timezone_set('UTC');

require '../vendor/autoload.php';

if (file_exists('coverage.php')) {
    include_once 'coverage.php';
}

$app = new \atk4\ui\App();

if (file_exists('coverage.php')) {
    $app->addHook('beforeExit', function () {
        coverage();
    });
}

$app->title = 'Agile UI Demo v'.$app->version;

if (file_exists('../public/atkjs-ui.min.js')) {
    $app->cdn['atk'] = '../public';
}

$app->initLayout($app->stickyGET('layout') ?: 'Admin');

$layout = $app->layout;

if (isset($layout->leftMenu)) {
    $layout->leftMenu->addItem(['Welcome Page', 'icon' => 'gift'], ['index']);
    $layout->leftMenu->addItem(['Layouts', 'icon' => 'object group'], ['layouts']);

    $form = $layout->leftMenu->addGroup(['Form', 'icon' => 'edit']);
    $form->addItem('Basics and Layouting', ['form']);
    $form->addItem(['Form Sections', 'icon'=>'yellow star'], ['form-section']);
    $form->addItem(['Input Fields', 'icon'=>'yellow star'], ['field2']);
    $form->addItem('Input Field Decoration', ['field']);
    $form->addItem(['File Uploading'], ['upload']);
    $form->addItem(['Checkboxes'], ['checkbox']);
    $form->addItem('Data Integration', ['form2']);
    $form->addItem('Form Multi-column layout', ['form3']);
    $form->addItem(['Integration with Columns'], ['form5']);
    $form->addItem(['AutoComplete Field', 'icon'=>'yellow star'], ['autocomplete']);
    $form->addItem(['Value Selectors'], ['form6']);
    $form->addItem(['Conditional Fields'], ['jscondform']);

    $form = $layout->leftMenu->addGroup(['Grid and Table', 'icon' => 'table']);
    $form->addItem('Data table with formatted columns', ['table']);
    $form->addItem(['Advanced table examples', 'icon'=>'yellow star'], ['table2']);
    $form->addItem('Table interractions', ['multitable']);
    $form->addItem(['Column Menus'], ['tablecolumnmenu']);
    $form->addItem(['Column Filters'], ['tablefilter']);
    $form->addItem('Grid - Table+Bar+Search+Paginator', ['grid']);
    $form->addItem('CRUD - Full editing solution', ['crud']);
    $form->addItem('CRUD with Array Persistence', ['crud3', 'icon' => 'star']);

    $basic = $layout->leftMenu->addGroup(['Basics', 'icon' => 'cubes']);
    $basic->addItem('View', ['view']);
    $basic->addItem('Lister', ['lister']);
    $basic->addItem('Button', ['button']);
    $basic->addItem('Header', ['header']);
    $basic->addItem('Message', ['message']);
    $basic->addItem('Labels', ['label']);
    $basic->addItem('Menu', ['menu']);
    $basic->addItem('BreadCrumb', ['breadcrumb']);
    $basic->addItem('Tabs', ['tabs']);
    $basic->addItem(['Accordion', 'icon'=>'yellow star'], ['accordion']);
    $basic->addItem(['Columns'], ['columns']);
    $basic->addItem('Paginator', ['paginator']);

    $basic = $layout->leftMenu->addGroup(['Interactivity', 'icon' => 'talk']);
    $basic->addItem(['Wizard'], ['wizard']);
    $basic->addItem('JavaScript Events', ['js']);
    $basic->addItem('Element Reloading', ['reloading']);
    $basic->addItem(['Background PHP Jobs (SSE)'], ['sse']);
    $basic->addItem(['Progress Bar'], ['progress']);
    $basic->addItem(['Loader'], ['loader']);
    $basic->addItem(['Console'], ['console']);
    $basic->addItem('Notifier', ['notify']);
    $basic->addItem(['Toast', 'icon'=>'yellow star'], ['toast']);
    $basic->addItem(['Pop-up'], ['popup']);
    $basic->addItem(['Modal View'], ['modal2']);
    $basic->addItem('Dynamic jsModal', ['modal']);
    $basic->addItem(['Dynamic scroll', 'icon'=>'yellow star'], ['scroll-lister']);
    $basic->addItem('Sticky GET', ['sticky']);
    $basic->addItem('Recursive Views', ['recursive']);

    //$basic->addItem('Virtual Page', ['virtual']);

    $f = basename($_SERVER['PHP_SELF']);

    //$url = 'https://github.com/atk4/ui/blob/feature/grid-part2/demos/';
    $url = 'https://github.com/atk4/ui/blob/develop/demos/';

    // Would be nice if this would be a link.
    $layout->menu->addItem()->add(['Button', 'View Source', 'teal', 'icon' => 'github'])
        ->setAttr('target', '_blank')->on('click', new \atk4\ui\jsExpression('document.location=[];', [$url.$f]));

    $img = 'https://raw.githubusercontent.com/atk4/ui/07208a0af84109f0d6e3553e242720d8aeedb784/public/logo.png';
}

require_once 'somedatadef.php';
