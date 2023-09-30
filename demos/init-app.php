<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Data\Persistence;
use Atk4\Ui\App;
use Atk4\Ui\Behat\CoverageUtil;
use Atk4\Ui\Button;
use Atk4\Ui\Exception;
use Atk4\Ui\Layout;

date_default_timezone_set('UTC');

require_once __DIR__ . '/init-autoloader.php';

// collect coverage for HTTP tests 1/2
$coverageSaveFx = null;
if (is_dir(__DIR__ . '/../coverage') && !CoverageUtil::isCalledFromPhpunit()) {
    CoverageUtil::startFromPhpunitConfig(__DIR__ . '/..');
    $coverageSaveFx = static function (): void {
        CoverageUtil::saveData(__DIR__ . '/../coverage');
    };
}

$app = new App([
    'callExit' => (bool) ($_GET['APP_CALL_EXIT'] ?? true),
    'catchExceptions' => (bool) ($_GET['APP_CATCH_EXCEPTIONS'] ?? true),
    'alwaysRun' => (bool) ($_GET['APP_ALWAYS_RUN'] ?? true),
]);
$app->title = 'Agile UI Demo v' . $app->version;

unset($_SERVER);
unset($_GET);
unset($_POST);
unset($_FILES);
if (isset($_COOKIE)) { // @phpstan-ignore-line https://github.com/phpstan/phpstan/issues/9953
    $sessionCookieName = function_exists('session_name') ? session_name() : false;
    foreach (array_keys($_COOKIE) as $k) {
        if ($k !== $sessionCookieName) {
            unset($_COOKIE[$k]);
        }
    }
    if ($_COOKIE === []) {
        unset($_COOKIE);
    }
}
unset($_SESSION);

if ($app->callExit !== true) {
    $app->stickyGet('APP_CALL_EXIT');
}

if ($app->catchExceptions !== true) {
    $app->stickyGet('APP_CATCH_EXCEPTIONS');
}

// collect coverage for HTTP tests 2/2
if ($coverageSaveFx !== null) {
    $app->onHook(App::HOOK_BEFORE_EXIT, $coverageSaveFx);
}
unset($coverageSaveFx);

final class AnonymousClassNameCache
{
    /** @var array<string, class-string> */
    private static $classNameByFxHash = [];

    private function __construct() {}

    /**
     * @template T of object
     *
     * @param \Closure(): T $createAnonymousClassFx
     *
     * @return class-string<T>
     */
    public static function get_class(\Closure $createAnonymousClassFx): string
    {
        $fxRefl = new \ReflectionFunction($createAnonymousClassFx);
        $fxHash = $fxRefl->getFileName() . ':' . $fxRefl->getStartLine() . '-' . $fxRefl->getEndLine();

        if (!isset(self::$classNameByFxHash[$fxHash])) {
            self::$classNameByFxHash[$fxHash] = get_class($createAnonymousClassFx());
        }

        return self::$classNameByFxHash[$fxHash];
    }
}

try {
    /** @var Persistence\Sql $db */
    require_once __DIR__ . '/init-db.php';
    $app->db = $db;
    unset($db);
} catch (\Throwable $e) {
    throw new Exception('Database error: ' . $e->getMessage());
}

[$rootUrl, $relUrl] = preg_split('~(?<=/)(?=demos(?:/|$))~s', $app->getRequest()->getUri()->getPath(), 3);
$demosUrl = $rootUrl . 'demos/';

// allow custom layout override
$app->initLayout([!$app->hasRequestQueryParam('layout') ? Layout\Maestro::class : $app->stickyGet('layout')]);

$layout = $app->layout;
if ($layout instanceof Layout\NavigableInterface) {
    $layout->addMenuItem(['Welcome to Agile Toolkit', 'icon' => 'gift'], [$demosUrl . 'index']);

    $path = $demosUrl . 'layout/';
    $menu = $layout->addMenuGroup(['Layout', 'icon' => 'object group']);
    $layout->addMenuItem(['Layouts'], [$path . 'layouts'], $menu);
    $layout->addMenuItem(['Sliding Panel'], [$path . 'layout-panel'], $menu);

    $path = $demosUrl . 'basic/';
    $menu = $layout->addMenuGroup(['Basics', 'icon' => 'cubes']);
    $layout->addMenuItem('View', [$path . 'view'], $menu);
    $layout->addMenuItem('Button', [$path . 'button'], $menu);
    $layout->addMenuItem('Header', [$path . 'header'], $menu);
    $layout->addMenuItem('Message', [$path . 'message'], $menu);
    $layout->addMenuItem('Labels', [$path . 'label'], $menu);
    $layout->addMenuItem('Menu', [$path . 'menu'], $menu);
    $layout->addMenuItem('Breadcrumb', [$path . 'breadcrumb'], $menu);
    $layout->addMenuItem(['Columns'], [$path . 'columns'], $menu);
    $layout->addMenuItem(['Grid Layout'], [$path . 'grid-layout'], $menu);

    $path = $demosUrl . 'form/';
    $menu = $layout->addMenuGroup(['Form', 'icon' => 'edit']);
    $layout->addMenuItem('Basics and Layouting', [$path . 'form'], $menu);
    $layout->addMenuItem('Data Integration', [$path . 'form2'], $menu);
    $layout->addMenuItem(['Form Sections'], [$path . 'form-section'], $menu);
    $layout->addMenuItem('Form Multi-column layout', [$path . 'form3'], $menu);
    $layout->addMenuItem(['Integration with Columns'], [$path . 'form5'], $menu);
    $layout->addMenuItem(['HTML Layout'], [$path . 'html-layout'], $menu);
    $layout->addMenuItem(['Conditional Fields'], [$path . 'jscondform'], $menu);

    $path = $demosUrl . 'form-control/';
    $menu = $layout->addMenuGroup(['Form Controls', 'icon' => 'keyboard outline']);
    $layout->addMenuItem(['Input'], [$path . 'input2'], $menu);
    $layout->addMenuItem('Input Decoration', [$path . 'input'], $menu);
    $layout->addMenuItem('Calendar', [$path . 'calendar'], $menu);
    $layout->addMenuItem(['Checkboxes'], [$path . 'checkbox'], $menu);
    $layout->addMenuItem(['Value Selectors'], [$path . 'form6'], $menu);
    $layout->addMenuItem(['Lookup'], [$path . 'lookup'], $menu);
    $layout->addMenuItem(['Lookup Dependency'], [$path . 'lookup-dep'], $menu);
    $layout->addMenuItem(['Dropdown'], [$path . 'dropdown-plus'], $menu);
    $layout->addMenuItem(['File Upload'], [$path . 'upload'], $menu);
    $layout->addMenuItem(['Multi Line'], [$path . 'multiline'], $menu);
    $layout->addMenuItem(['Tree Selector'], [$path . 'tree-item-selector'], $menu);
    $layout->addMenuItem(['Scope Builder'], [$path . 'scope-builder'], $menu);

    $path = $demosUrl . 'collection/';
    $menu = $layout->addMenuGroup(['Data Collection', 'icon' => 'table']);
    $layout->addMenuItem('Data table with formatted columns', [$path . 'table'], $menu);
    $layout->addMenuItem(['Advanced table examples'], [$path . 'table2'], $menu);
    $layout->addMenuItem('Table interractions', [$path . 'multitable'], $menu);
    $layout->addMenuItem(['Column Menus'], [$path . 'tablecolumnmenu'], $menu);
    $layout->addMenuItem(['Column Filters'], [$path . 'tablefilter'], $menu);
    $layout->addMenuItem('Grid - Table+Bar+Search+Paginator', [$path . 'grid'], $menu);
    $layout->addMenuItem('Crud - Full editing solution', [$path . 'crud'], $menu);
    $layout->addMenuItem(['Crud - /w Array Persistence'], [$path . 'crud3'], $menu);
    $layout->addMenuItem('Card Deck - /w custom actions', [$path . 'card-deck'], $menu);
    $layout->addMenuItem(['Lister'], [$path . 'lister-ipp'], $menu);
    $layout->addMenuItem(['Table column decorator from model'], [$path . 'tablecolumns'], $menu);

    $path = $demosUrl . 'data-action/';
    $menu = $layout->addMenuGroup(['Data Action Executor', 'icon' => 'wrench']);
    $layout->addMenuItem(['Executor Examples'], [$path . 'actions'], $menu);
    $layout->addMenuItem(['Assign action to event'], [$path . 'jsactions'], $menu);
    $layout->addMenuItem(['Assign action to button (Modal)'], [$path . 'jsactions2'], $menu);
    $layout->addMenuItem(['Assign action to button (Panel)'], [$path . 'jsactions-panel'], $menu);
    $layout->addMenuItem(['Assign action to button (V. Page)'], [$path . 'jsactions-vp'], $menu);
    $layout->addMenuItem(['Execute from Grid'], [$path . 'jsactionsgrid'], $menu);
    $layout->addMenuItem(['Execute from Crud'], [$path . 'jsactionscrud'], $menu);
    $layout->addMenuItem(['Executor Factory'], [$path . 'factory'], $menu);

    $path = $demosUrl . 'interactive/';
    $menu = $layout->addMenuGroup(['Interactive', 'icon' => 'talk']);
    $layout->addMenuItem('Tabs', [$path . 'tabs'], $menu);
    $layout->addMenuItem('Card', [$path . 'card'], $menu);
    $layout->addMenuItem('Card Table', [$path . 'cardtable'], $menu);
    $layout->addMenuItem(['Accordion'], [$path . 'accordion'], $menu);
    $layout->addMenuItem(['Wizard'], [$path . 'wizard'], $menu);
    $layout->addMenuItem(['Virtual Page'], [$path . 'virtual'], $menu);
    $layout->addMenuItem('Modal', [$path . 'modal'], $menu);
    $layout->addMenuItem(['Loader'], [$path . 'loader'], $menu);
    $layout->addMenuItem(['Console'], [$path . 'console'], $menu);
    $layout->addMenuItem(['Dynamic scroll'], [$path . 'scroll-lister'], $menu);
    $layout->addMenuItem(['Background PHP Jobs (SSE)'], [$path . 'sse'], $menu);
    $layout->addMenuItem(['Progress Bar'], [$path . 'progress'], $menu);
    $layout->addMenuItem(['Pop-up'], [$path . 'popup'], $menu);
    $layout->addMenuItem(['Toast'], [$path . 'toast'], $menu);
    $layout->addMenuItem('Paginator', [$path . 'paginator'], $menu);
    $layout->addMenuItem(['Drag sorting'], [$path . 'jssortable'], $menu);

    $path = $demosUrl . 'javascript/';
    $menu = $layout->addMenuGroup(['Javascript', 'icon' => 'code']);
    $layout->addMenuItem('Events', [$path . 'js'], $menu);
    $layout->addMenuItem('Element Reloading', [$path . 'reloading'], $menu);
    $layout->addMenuItem('Vue Integration', [$path . 'vue-component'], $menu);

    $path = $demosUrl . 'others/';
    $menu = $layout->addMenuGroup(['Others', 'icon' => 'plus']);
    $layout->addMenuItem('Sticky GET', [$path . 'sticky'], $menu);
    $layout->addMenuItem('More Sticky', [$path . 'sticky2'], $menu);
    $layout->addMenuItem('Recursive Views', [$path . 'recursive'], $menu);

    // view demo source page on Github
    Button::addTo($layout->menu->addItem()->addClass('aligned right'), ['View Source', 'class.teal' => true, 'icon' => 'github'])
        ->on('click', $app->jsRedirect('https://github.com/atk4/ui/blob/develop/' . $relUrl, true));
}
unset($layout, $rootUrl, $relUrl, $demosUrl, $path, $menu);
