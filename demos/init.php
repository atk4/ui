<?php

date_default_timezone_set('UTC');

require '../vendor/autoload.php';

/* START - PHPUNIT & COVERAGE SETUP */
if (file_exists('coverage.php')) {
    include_once 'coverage.php';
}

class Demo extends \atk4\ui\Columns
{
    public $left;
    public $right;
    public static $isInitialized = false;
    public $highlightDefaultStyle = 'dark';

    public function init()
    {
        parent::init();
        $this->addClass('celled');

        $this->left = $this->addColumn();
        $this->right = $this->addColumn();
    }

    public function setCode($code, $lang = 'php')
    {
        $this->highLightCode();
        $this->left->add(['element' => 'pre'])->add(['element' => 'code'])->addClass($lang)->set($code);
        $app = $this->right;
        $app->db = $this->app->db;
        eval($code);
    }

    public function highLightCode()
    {
        if (!self::$isInitialized) {
            $this->app->requireCSS('//cdn.jsdelivr.net/gh/highlightjs/cdn-release@9.16.2/build/styles/' . $this->highlightDefaultStyle . '.min.css');
            $this->app->requireJS('//cdn.jsdelivr.net/gh/highlightjs/cdn-release@9.16.2/build/highlight.min.js');
            $this->js(true, (new \atk4\ui\jsChain('hljs'))->initHighlighting());
            self::$isInitialized = true;
        }
    }
}

$app = new \atk4\ui\App([
    'call_exit'        => isset($_GET['APP_CALL_EXIT']) && $_GET['APP_CALL_EXIT'] == 0 ? false : true,
    'catch_exceptions' => isset($_GET['APP_CATCH_EXCEPTIONS']) && $_GET['APP_CATCH_EXCEPTIONS'] == 0 ? false : true,
]);

if ($app->call_exit !== true) {
    $app->stickyGet('APP_CALL_EXIT');
}

if ($app->catch_exceptions !== true) {
    $app->stickyGet('APP_CATCH_EXCEPTIONS');
}

if (file_exists('coverage.php')) {
    $app->addHook('beforeExit', function () {
        coverage();
    });
}
/* END - PHPUNIT & COVERAGE SETUP */

$app->title = 'Agile UI Demo v' . $app->version;

if (file_exists('../public/atkjs-ui.min.js')) {
    $app->cdn['atk'] = '../public';
}

$app->initLayout($app->stickyGET('layout') ?: 'Admin');

$layout = $app->layout;

if (isset($layout->leftMenu)) {
    $layout->leftMenu->addItem(['Welcome to Agile Toolkit', 'icon' => 'gift'], ['index']);
    $layout->leftMenu->addItem(['Layouts', 'icon' => 'object group'], ['layouts']);

    $form = $layout->leftMenu->addGroup(['Form', 'icon' => 'edit']);
    $form->addItem('Basics and Layouting', ['form']);
    $form->addItem(['Form Sections', 'icon' => 'yellow star'], ['form-section']);
    $form->addItem(['Input Fields', 'icon' => 'yellow star'], ['field2']);
    $form->addItem('Input Field Decoration', ['field']);
    $form->addItem(['File Uploading'], ['upload']);
    $form->addItem(['Checkboxes'], ['checkbox']);
    $form->addItem('Data Integration', ['form2']);
    $form->addItem('Form Multi-column layout', ['form3']);
    $form->addItem(['Integration with Columns'], ['form5']);
    $form->addItem(['AutoComplete Field', 'icon' => 'yellow star'], ['autocomplete']);
    $form->addItem(['DropDown Field'], ['dropdown-plus']);
    $form->addItem(['Value Selectors'], ['form6']);
    $form->addItem(['Conditional Fields'], ['jscondform']);

    $form = $layout->leftMenu->addGroup(['Grid and Table', 'icon' => 'table']);
    $form->addItem('Data table with formatted columns', ['table']);
    $form->addItem(['Advanced table examples', 'icon' => 'yellow star'], ['table2']);
    $form->addItem('Table interractions', ['multitable']);
    $form->addItem(['Column Menus'], ['tablecolumnmenu']);
    $form->addItem(['Column Filters'], ['tablefilter']);
    $form->addItem('Grid - Table+Bar+Search+Paginator', ['grid']);
    $form->addItem('CRUD - Full editing solution', ['crud']);
    $form->addItem(['CRUD with Array Persistence', 'icon' => 'yellow star'], ['crud3']);
    $form->addItem(['Grid Layout', 'icon' => 'yellow star'], ['grid-layout']);
    $form->addItem(['Actions - Integration Examples', 'icon' => 'yellow star'], ['actions']);

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
    $basic->addItem(['Accordion', 'icon' => 'yellow star'], ['accordion']);
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
    $basic->addItem(['Toast', 'icon' => 'yellow star'], ['toast']);
    $basic->addItem(['Pop-up'], ['popup']);
    $basic->addItem(['Modal View'], ['modal2']);
    $basic->addItem('Dynamic jsModal', ['modal']);
    $basic->addItem(['Dynamic scroll', 'icon' => 'yellow star'], ['scroll-lister']);
    $basic->addItem('Sticky GET', ['sticky']);
    $basic->addItem('Recursive Views', ['recursive']);

    //$basic->addItem('Virtual Page', ['virtual']);

    $f = basename($_SERVER['PHP_SELF']);

    //$url = 'https://github.com/atk4/ui/blob/feature/grid-part2/demos/';
    $url = 'https://github.com/atk4/ui/blob/develop/demos/';

    // Would be nice if this would be a link.
    $layout->menu->addItem()->add(['Button', 'View Source', 'teal', 'icon' => 'github'])
        ->setAttr('target', '_blank')->on('click', new \atk4\ui\jsExpression('document.location=[];', [$url . $f]));

    $img = 'https://raw.githubusercontent.com/atk4/ui/07208a0af84109f0d6e3553e242720d8aeedb784/public/logo.png';
}

require_once 'somedatadef.php';
