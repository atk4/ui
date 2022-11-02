<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Core\AppScopeTrait;
use Atk4\Core\DiContainerTrait;
use Atk4\Core\DynamicMethodTrait;
use Atk4\Core\ExceptionRenderer;
use Atk4\Core\Factory;
use Atk4\Core\HookTrait;
use Atk4\Core\InitializerTrait;
use Atk4\Core\TraitUtil;
use Atk4\Core\WarnDynamicPropertyTrait;
use Atk4\Data\Persistence;
use Atk4\Ui\Exception\ExitApplicationError;
use Atk4\Ui\Exception\LateOutputError;
use Atk4\Ui\Exception\UnhandledCallbackExceptionError;
use Atk4\Ui\Persistence\Ui as UiPersistence;
use Atk4\Ui\UserAction\ExecutorFactory;
use Psr\Log\LoggerInterface;

class App
{
    use AppScopeTrait;
    use DiContainerTrait;
    use DynamicMethodTrait;
    use HookTrait;
    use InitializerTrait {
        init as private _init;
    }

    public const HOOK_BEFORE_EXIT = self::class . '@beforeExit';
    public const HOOK_BEFORE_RENDER = self::class . '@beforeRender';

    protected const HEADER_STATUS_CODE = 'atk4-status-code';

    /** @var array|false Location where to load JS/CSS files */
    public $cdn = [
        'atk' => '/public',
        'jquery' => '/public/external/jquery/dist',
        'fomantic-ui' => '/public/external/fomantic-ui/dist',
        'flatpickr' => '/public/external/flatpickr/dist',
        'chart.js' => '/public/external/chart.js/dist', // for atk4/chart
    ];

    /** @var ExecutorFactory App wide executor factory object for Model user action. */
    protected $executorFactory;

    /**
     * @var string Version of Agile UI
     *
     * @TODO remove, no longer needed for CDN versioning as we bundle all resources
     */
    public $version = '4.0-dev';

    /** @var string Name of application */
    public $title = 'Agile UI - Untitled Application';

    /** @var Layout the top-most view object */
    public $layout;

    /** @var string|array Set one or more directories where templates should reside. */
    public $templateDir;

    /** @var bool Will replace an exception handler with our own, that will output errors nicely. */
    public $catchExceptions = true;

    /** @var bool Will display error if callback wasn't triggered. */
    public $catchRunawayCallbacks = true;

    /** @var bool Will always run application even if developer didn't explicitly executed run();. */
    public $alwaysRun = true;

    /**
     * Will be set to true after app->run() is called, which may be done automatically
     * on exit.
     */
    public bool $runCalled = false;

    /**
     * Will be set to true, when exit is called. Sometimes exit is intercepted by shutdown
     * handler and we don't want to execute 'beforeExit' multiple times.
     */
    private bool $exitCalled = false;

    public bool $isRendering = false;

    /** @var UiPersistence */
    public $uiPersistence;

    /** @var View|null For internal use */
    public $html;

    /** @var LoggerInterface|null Target for objects with DebugTrait */
    public $logger;

    /** @var Persistence|Persistence\Sql */
    public $db;

    /** @var App\SessionManager */
    public $session;

    /** @var array<string, string> Extra HTTP headers to send on exit. */
    protected array $responseHeaders = [
        self::HEADER_STATUS_CODE => '200',
        'cache-control' => 'no-store', // disable caching by default
    ];

    /** @var array<string, View> Modal view that need to be rendered using json output. */
    private $portals = [];

    /**
     * @var string used in method App::url to build the url
     *
     * Used only in method App::url
     * Remove and re-add the extension of the file during parsing requests and building urls
     */
    protected $urlBuildingExt = '.php';

    /** @var bool Call exit in place of throw Exception when Application need to exit. */
    public $callExit = true;

    /** @var string|null */
    public $page;

    /** @var array global sticky arguments */
    protected array $stickyGetArguments = [
        '__atk_json' => false,
        '__atk_tab' => false,
    ];

    /** @var class-string */
    public $templateClass = HtmlTemplate::class;

    public function __construct(array $defaults = [])
    {
        $this->setApp($this);

        $this->setDefaults($defaults);

        $this->setupTemplateDirs();

        foreach ($this->cdn as $k => $v) {
            if (str_starts_with($v, '/') && !str_starts_with($v, '//')) {
                $this->cdn[$k] = $this->createRequestPathFromLocalPath(__DIR__ . '/..' . $v);
            }
        }

        // Set our exception handler
        if ($this->catchExceptions) {
            set_exception_handler(\Closure::fromCallable([$this, 'caughtException']));
            set_error_handler(
                static function (int $severity, string $msg, string $file, int $line): bool {
                    if ((error_reporting() & ~(\PHP_MAJOR_VERSION >= 8 ? 4437 : 0)) === 0) {
                        $isFirstFrame = true;
                        foreach (array_slice(debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS, 10), 1) as $frame) {
                            // allow to suppress any warning outside Atk4
                            if ($isFirstFrame) {
                                $isFirstFrame = false;
                                if (!isset($frame['class']) || !str_starts_with($frame['class'], 'Atk4\\')) {
                                    return false;
                                }
                            }

                            // allow to suppress undefined property warning
                            if (isset($frame['class']) && TraitUtil::hasTrait($frame['class'], WarnDynamicPropertyTrait::class)
                                && $frame['function'] === 'warnPropertyDoesNotExist') {
                                return false;
                            }
                        }
                    }

                    throw new \ErrorException($msg, 0, $severity, $file, $line);
                },
                \E_ALL
            );
            $this->outputResponseUnsafe('', [self::HEADER_STATUS_CODE => '500']);
        }

        // Always run app on shutdown
        if ($this->alwaysRun) {
            $this->setupAlwaysRun();
        }

        if ($this->uiPersistence === null) {
            $this->uiPersistence = new UiPersistence();
        }

        if ($this->session === null) {
            $this->session = new App\SessionManager();
        }

        // setting up default executor factory.
        $this->executorFactory = Factory::factory([ExecutorFactory::class]);
    }

    /**
     * Register a portal view.
     * Fomantic-ui Modal or atk Panel are teleported in HTML template
     * within specific location. This will keep track
     * of them when terminating app using json.
     *
     * @param Modal|Panel\Right $portal
     */
    public function registerPortals($portal): void
    {
        // TODO in https://github.com/atk4/ui/pull/1771 it has been discovered this method causes DOM code duplication,
        // for some reasons, it seems even not needed, at least all Unit & Behat tests pass
        // must be investigated
        // $this->portals[$portal->name] = $portal;
    }

    public function setExecutorFactory(ExecutorFactory $factory): void
    {
        $this->executorFactory = $factory;
    }

    public function getExecutorFactory(): ExecutorFactory
    {
        return $this->executorFactory;
    }

    protected function setupTemplateDirs(): void
    {
        if ($this->templateDir === null) {
            $this->templateDir = [];
        } elseif (!is_array($this->templateDir)) {
            $this->templateDir = [$this->templateDir];
        }

        $this->templateDir[] = dirname(__DIR__) . '/template';
    }

    protected function callBeforeExit(): void
    {
        if (!$this->exitCalled) {
            $this->exitCalled = true;
            $this->hook(self::HOOK_BEFORE_EXIT);
        }
    }

    /**
     * @return never
     */
    public function callExit(): void
    {
        $this->callBeforeExit();

        if (!$this->callExit) {
            // case process is not in shutdown mode
            // App as already done everything
            // App need to stop output
            // set_handler to catch/trap any exception
            set_exception_handler(static function (\Throwable $t): void {});
            // raise exception to be trapped and stop execution
            throw new ExitApplicationError();
        }

        exit;
    }

    /**
     * Catch exception.
     */
    public function caughtException(\Throwable $exception): void
    {
        if ($exception instanceof LateOutputError) {
            $this->outputLateOutputError($exception);
        }

        while ($exception instanceof UnhandledCallbackExceptionError) {
            $exception = $exception->getPrevious();
        }

        $this->catchRunawayCallbacks = false;

        // just replace layout to avoid any extended App->_construct problems
        // it will maintain everything as in the original app StickyGet, logger, Events
        $this->html = null;
        $this->initLayout([Layout\Centered::class]);

        $this->layout->template->dangerouslySetHtml('Content', $this->renderExceptionHtml($exception));

        // remove header
        $this->layout->template->tryDel('Header');

        if (($this->isJsUrlRequest() || strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest')
                && !isset($_GET['__atk_tab'])) {
            $this->outputResponseJson([
                'success' => false,
                'message' => $this->layout->getHtml(),
            ]);
        } else {
            $this->setResponseStatusCode(500);
            $this->run();
        }

        // Process is already in shutdown/stop
        // no need of call exit function
        $this->callBeforeExit();
    }

    /**
     * Normalize HTTP headers to associative array with LC keys.
     *
     * @param array<string, string> $headers
     *
     * @return array<string, string>
     */
    protected function normalizeHeaders(array $headers): array
    {
        $res = [];
        foreach ($headers as $k => $v) {
            if (is_numeric($k) && ($p = strpos($v, ':')) !== false) {
                $k = substr($v, 0, $p);
                $v = substr($v, $p + 1);
            }

            $res[strtolower(trim($k))] = trim($v);
        }

        return $res;
    }

    /**
     * @return $this
     */
    public function setResponseStatusCode(int $statusCode): self
    {
        $this->setResponseHeader(self::HEADER_STATUS_CODE, (string) $statusCode);

        return $this;
    }

    /**
     * @return $this
     */
    public function setResponseHeader(string $name, string $value): self
    {
        $arr = $this->normalizeHeaders([$name => $value]);
        $value = reset($arr);
        $name = array_key_first($arr);

        if ($value !== '') {
            $this->responseHeaders[$name] = $value;
        } else {
            unset($this->responseHeaders[$name]);
        }

        return $this;
    }

    /**
     * Will perform a preemptive output and terminate. Do not use this
     * directly, instead call it form Callback, JsCallback or similar
     * other classes.
     *
     * @param string|array          $output  Array type is supported only for JSON response
     * @param array<string, string> $headers content-type header must be always set or consider using App::terminateHtml() or App::terminateJson() methods
     *
     * @return never
     */
    public function terminate($output = '', array $headers = []): void
    {
        $headers = $this->normalizeHeaders($headers);
        if (!isset($headers['content-type'])) {
            $this->responseHeaders = $this->normalizeHeaders($this->responseHeaders);
            if (!isset($this->responseHeaders['content-type'])) {
                throw new Exception('Content type must be always set');
            }

            $headers['content-type'] = $this->responseHeaders['content-type'];
        }

        $type = preg_replace('~;.*~', '', strtolower($headers['content-type'])); // in LC without charset

        if ($type === 'application/json') {
            if (is_string($output)) {
                $output = $this->decodeJson($output);
            }
            $output['portals'] = $this->getRenderedPortals();

            $this->outputResponseJson($output, $headers);
        } elseif (isset($_GET['__atk_tab']) && $type === 'text/html') {
            // ugly hack for TABS
            // because Fomantic-UI tab only deal with html and not JSON
            // we need to hack output to include app modal.
            $ids = [];
            $remove_function = '';
            foreach ($this->getRenderedPortals() as $key => $modal) {
                // add modal rendering to output
                $ids[] = '#' . $key;
                $output['atkjs'] .= '; ' . $modal['js'];
                $output['html'] .= $modal['html'];
            }
            if (count($ids) > 0) {
                $remove_function = '$(\'.ui.dimmer.modals.page, .atk-side-panels\').find(\'' . implode(', ', $ids) . '\').remove();';
            }

            $output = $this->getTag('script', [], '$(function () {' . $remove_function . $output['atkjs'] . '});')
                . $output['html'];

            $this->outputResponseHtml($output, $headers);
        } elseif ($type === 'text/html') {
            $this->outputResponseHtml($output, $headers);
        } else {
            $this->outputResponse($output, $headers);
        }

        $this->runCalled = true; // prevent shutdown function from triggering
        $this->callExit();
    }

    /**
     * @param string|array|View|HtmlTemplate $output
     *
     * @return never
     */
    public function terminateHtml($output, array $headers = []): void
    {
        if ($output instanceof View) {
            $output = $output->render();
        } elseif ($output instanceof HtmlTemplate) {
            $output = $output->renderToHtml();
        }

        $this->terminate(
            $output,
            array_merge($this->normalizeHeaders($headers), ['content-type' => 'text/html'])
        );
    }

    /**
     * @param string|array|View $output
     *
     * @return never
     */
    public function terminateJson($output, array $headers = []): void
    {
        if ($output instanceof View) {
            $output = $output->renderToJsonArr();
        }

        $this->terminate(
            $output,
            array_merge($this->normalizeHeaders($headers), ['content-type' => 'application/json'])
        );
    }

    /**
     * Initializes layout.
     *
     * @param Layout|array $seed
     *
     * @return $this
     */
    public function initLayout($seed)
    {
        $layout = Layout::fromSeed($seed);
        $layout->setApp($this);

        if ($this->html === null) {
            $this->html = new View(['defaultTemplate' => 'html.html']);
            $this->html->setApp($this);
            $this->html->invokeInit();
        }

        $this->layout = $this->html->add($layout); // @phpstan-ignore-line

        $this->initIncludes();

        return $this;
    }

    /**
     * Initialize JS and CSS includes.
     */
    public function initIncludes(): void
    {
        /** @var bool */
        $minified = true;

        // jQuery
        $this->requireJs($this->cdn['jquery'] . '/jquery' . ($minified ? '.min' : '') . '.js');

        // Fomantic-UI
        $this->requireJs($this->cdn['fomantic-ui'] . '/semantic' . ($minified ? '.min' : '') . '.js');
        $this->requireCss($this->cdn['fomantic-ui'] . '/semantic' . ($minified ? '.min' : '') . '.css');

        // flatpickr - TODO should be load only when needed
        // needs https://github.com/atk4/ui/issues/1875
        $this->requireJs($this->cdn['flatpickr'] . '/flatpickr' . ($minified ? '.min' : '') . '.js');
        $this->requireCss($this->cdn['flatpickr'] . '/flatpickr' . ($minified ? '.min' : '') . '.css');
        if ($this->uiPersistence->locale !== 'en') {
            $this->requireJs($this->cdn['flatpickr'] . '/l10n/' . $this->uiPersistence->locale . '.js');
            $this->html->js(true, new JsExpression('flatpickr.localize(window.flatpickr.l10ns.' . $this->uiPersistence->locale . ')'));
        }

        // Agile UI
        $this->requireJs($this->cdn['atk'] . '/js/atkjs-ui' . ($minified ? '.min' : '') . '.js');
        $this->requireCss($this->cdn['atk'] . '/css/agileui.min.css');

        // Set js bundle dynamic loading path.
        $this->html->template->tryDangerouslySetHtml(
            'InitJsBundle',
            (new JsExpression('window.__atkBundlePublicPath = [];', [$this->cdn['atk']]))->jsRender()
        );
    }

    /**
     * Adds a <style> block to the HTML Header. Not escaped. Try to avoid
     * and use file include instead.
     *
     * @param string $style CSS rules, like ".foo { background: red }".
     */
    public function addStyle($style): void
    {
        $this->html->template->dangerouslyAppendHtml('Head', $this->getTag('style', [], $style));
    }

    /**
     * Add a new object into the app. You will need to have Layout first.
     *
     * @param AbstractView      $object
     * @param string|array|null $region
     *
     * @return ($object is View ? View : AbstractView)
     */
    public function add($object, $region = null): AbstractView
    {
        if (!$this->layout) { // @phpstan-ignore-line
            throw (new Exception('App layout is missing'))
                ->addSolution('$app->initLayout() must be called first');
        }

        return $this->layout->add($object, $region);
    }

    /**
     * Runs app and echo rendered template.
     */
    public function run(): void
    {
        $isExitException = false;
        try {
            $this->runCalled = true;
            $this->hook(self::HOOK_BEFORE_RENDER);
            $this->isRendering = true;

            $this->html->template->set('title', $this->title);
            $this->html->renderAll();
            $this->html->template->dangerouslyAppendHtml('Head', $this->getTag('script', [], '$(function () {' . $this->html->getJs() . ';});'));
            $this->isRendering = false;

            if (isset($_GET[Callback::URL_QUERY_TARGET]) && $this->catchRunawayCallbacks) {
                throw (new Exception('Callback requested, but never reached. You may be missing some arguments in request URL.'))
                    ->addMoreInfo('callback', $_GET[Callback::URL_QUERY_TARGET]);
            }

            $output = $this->html->template->renderToHtml();
        } catch (ExitApplicationError $e) {
            $output = '';
            $isExitException = true;
        }

        if (!$this->exitCalled) { // output already sent by terminate()
            if ($this->isJsUrlRequest()) {
                $this->outputResponseJson($output);
            } else {
                $this->outputResponseHtml($output);
            }
        }

        if ($isExitException) {
            $this->callExit();
        }
    }

    /**
     * Initialize app.
     */
    protected function init(): void
    {
        $this->_init();
    }

    /**
     * Load template by template file name.
     *
     * @param string $filename
     *
     * @return HtmlTemplate
     */
    public function loadTemplate($filename)
    {
        $template = new $this->templateClass();
        $template->setApp($this);

        if ((['.' => true, '/' => true, '\\' => true][substr($filename, 0, 1)] ?? false) || str_contains($filename, ':\\')) {
            return $template->loadFromFile($filename);
        }

        $dirs = is_array($this->templateDir) ? $this->templateDir : [$this->templateDir];
        foreach ($dirs as $dir) {
            $t = $template->tryLoadFromFile($dir . '/' . $filename);
            if ($t !== false) {
                return $t;
            }
        }

        throw (new Exception('Cannot find template file'))
            ->addMoreInfo('filename', $filename)
            ->addMoreInfo('templateDir', $this->templateDir);
    }

    protected function getRequestUrl(): string
    {
        if (isset($_SERVER['HTTP_X_REWRITE_URL'])) { // IIS
            $requestUrl = $_SERVER['HTTP_X_REWRITE_URL'];
        } elseif (isset($_SERVER['REQUEST_URI'])) { // Apache
            $requestUrl = $_SERVER['REQUEST_URI'];
        } elseif (isset($_SERVER['ORIG_PATH_INFO'])) { // IIS 5.0, PHP as CGI
            $requestUrl = $_SERVER['ORIG_PATH_INFO'];
        // This one comes without QUERY string
        } else {
            $requestUrl = '';
        }
        $requestUrl = explode('?', $requestUrl, 2);

        return $requestUrl[0];
    }

    protected function createRequestPathFromLocalPath(string $localPath): string
    {
        static $requestUrlPath = null;
        static $requestLocalPath = null;
        if ($requestUrlPath === null) {
            if (\PHP_SAPI === 'cli') { // for phpunit
                $requestUrlPath = '/';
                $requestLocalPath = \Closure::bind(function () {
                    return dirname((new ExceptionRenderer\Html(new \Exception()))->getVendorDirectory());
                }, null, ExceptionRenderer\Html::class)();
            } else {
                $request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
                $requestUrlPath = $request->getBasePath();
                $requestLocalPath = $request->server->get('SCRIPT_FILENAME');
            }
        }
        $fs = new \Symfony\Component\Filesystem\Filesystem();
        $localPathRelative = $fs->makePathRelative($localPath, dirname($requestLocalPath));
        $res = '/' . $fs->makePathRelative($requestUrlPath . '/' . $localPathRelative, '/');
        // fix https://github.com/symfony/symfony/pull/40051
        if (str_ends_with($res, '/') && !str_ends_with($localPath, '/')) {
            $res = substr($res, 0, -1);
        }

        return $res;
    }

    /**
     * Make current get argument with specified name automatically appended to all generated URLs.
     */
    public function stickyGet(string $name, bool $isDeleting = false): ?string
    {
        $this->stickyGetArguments[$name] = !$isDeleting;

        return $_GET[$name] ?? null;
    }

    /**
     * Remove sticky GET which was set by stickyGet.
     */
    public function stickyForget(string $name): void
    {
        unset($this->stickyGetArguments[$name]);
    }

    /**
     * Build a URL that application can use for loading HTML data.
     *
     * @param array|string $page                URL as string or array with page name as first element and other GET arguments
     * @param bool         $useRequestUrl       Simply return $_SERVER['REQUEST_URI'] if needed
     * @param array        $extraRequestUrlArgs additional URL arguments, deleting sticky can delete them
     */
    public function url($page = [], $useRequestUrl = false, $extraRequestUrlArgs = []): string
    {
        if ($useRequestUrl) {
            $page = $_SERVER['REQUEST_URI'];
        }

        if ($this->page === null) {
            $requestUrl = $this->getRequestUrl();
            if (substr($requestUrl, -1, 1) === '/') {
                $this->page = 'index';
            } else {
                $this->page = basename($requestUrl, $this->urlBuildingExt);
            }
        }

        $pagePath = '';
        if (is_string($page)) {
            $page_arr = explode('?', $page, 2);
            $pagePath = $page_arr[0];
            parse_str($page_arr[1] ?? '', $page);
        } else {
            $pagePath = $page[0] ?? $this->page; // use current page by default
            unset($page[0]);
            if ($pagePath) {
                $pagePath .= $this->urlBuildingExt;
            }
        }

        $args = $extraRequestUrlArgs;

        // add sticky arguments
        foreach ($this->stickyGetArguments as $k => $v) {
            if ($v && isset($_GET[$k])) {
                $args[$k] = $_GET[$k];
            } else {
                unset($args[$k]);
            }
        }

        // add arguments
        foreach ($page as $k => $v) {
            if ($v === null || $v === false) {
                unset($args[$k]);
            } else {
                $args[$k] = $v;
            }
        }

        // put URL together
        $pageQuery = http_build_query($args, '', '&', \PHP_QUERY_RFC3986);
        $url = $pagePath . ($pageQuery ? '?' . $pageQuery : '');

        return $url;
    }

    /**
     * Build a URL that application can use for js call-backs. Some framework integration will use a different routing
     * mechanism for NON-HTML response.
     *
     * @param array|string $page                URL as string or array with page name as first element and other GET arguments
     * @param bool         $useRequestUrl       Simply return $_SERVER['REQUEST_URI'] if needed
     * @param array        $extraRequestUrlArgs additional URL arguments, deleting sticky can delete them
     */
    public function jsUrl($page = [], $useRequestUrl = false, $extraRequestUrlArgs = []): string
    {
        // append to the end but allow override
        $extraRequestUrlArgs = array_merge($extraRequestUrlArgs, ['__atk_json' => 1], $extraRequestUrlArgs);

        return $this->url($page, $useRequestUrl, $extraRequestUrlArgs);
    }

    /**
     * Request was made using App::jsUrl().
     */
    public function isJsUrlRequest(): bool
    {
        return isset($_GET['__atk_json']) && $_GET['__atk_json'] !== '0';
    }

    /**
     * Adds additional JS script include in application template.
     *
     * @param string $url
     * @param bool   $isAsync whether or not you want Async loading
     * @param bool   $isDefer whether or not you want Defer loading
     *
     * @return $this
     */
    public function requireJs($url, $isAsync = false, $isDefer = false)
    {
        $this->html->template->dangerouslyAppendHtml('Head', $this->getTag('script', ['src' => $url, 'defer' => $isDefer, 'async' => $isAsync], '') . "\n");

        return $this;
    }

    /**
     * Adds additional CSS stylesheet include in application template.
     *
     * @param string $url
     *
     * @return $this
     */
    public function requireCss($url)
    {
        $this->html->template->dangerouslyAppendHtml('Head', $this->getTag('link/', ['rel' => 'stylesheet', 'type' => 'text/css', 'href' => $url]) . "\n");

        return $this;
    }

    /**
     * A convenient wrapper for sending user to another page.
     *
     * @param array|string $page Destination page
     */
    public function redirect($page, bool $permanent = false): void
    {
        $this->terminateHtml('', ['location' => $this->url($page), self::HEADER_STATUS_CODE => $permanent ? '301' : '302']);
    }

    /**
     * Generate action for redirecting user to another page.
     *
     * @param string|array $page Destination URL or page/arguments
     */
    public function jsRedirect($page, bool $newWindow = false): JsExpression
    {
        return new JsExpression('window.open([], [])', [$this->url($page), $newWindow ? '_blank' : '_top']);
    }

    public function isVoidTag(string $tag): bool
    {
        return [
            'area' => true, 'base' => true, 'br' => true, 'col' => true, 'embed' => true,
            'hr' => true, 'img' => true, 'input' => true, 'link' => true, 'meta' => true,
            'param' => true, 'source' => true, 'track' => true, 'wbr' => true,
        ][strtolower($tag)] ?? false;
    }

    /**
     * Construct HTML tag with supplied attributes.
     *
     * $html = getTag('img/', ['src' => 'foo.gif', 'border' => 0])
     * --> "<img src="foo.gif" border="0">"
     *
     *
     * The following rules are respected:
     *
     * 1. all array key => val elements appear as attributes with value escaped.
     * getTag('input/', ['value' => 'he"llo'])
     * --> <input value="he&quot;llo">
     *
     * 2. true value will add attribute without value
     * getTag('td', ['nowrap' => true])
     * --> <td nowrap="nowrap">
     *
     * 3. false value will ignore the attribute
     * getTag('img/', ['src' => false])
     * --> <img>
     *
     * 4. passing key 0 => "val" will re-define the element itself
     * getTag('div', ['a', 'href' => 'picture'])
     * --> <a href="picture">
     *
     * 5. use '/' at end of tag to self-close it (self closing slash is not rendered because of HTML5 void tag)
     * getTag('img/', ['src' => 'foo.gif'])
     * --> <img src="foo.gif">
     *
     * 6. if main tag is self-closing, overriding it keeps it self-closing
     * getTag('img/', ['input', 'type' => 'picture'])
     * --> <input type="picture">
     *
     * 7. simple way to close tag. Any attributes to closing tags are ignored
     * getTag('/td')
     * --> </td>
     *
     * 7b. except for 0 => 'newtag'
     * getTag('/td', ['th', 'align' => 'left'])
     * --> </th>
     *
     * 8. using $value will add value inside tag. It will also encode value.
     * getTag('a', ['href' => 'foo.html'], 'click here >>')
     * --> <a href="foo.html">click here &gt;&gt;</a>
     *
     * 9. pass array as 3rd parameter to nest tags. Each element can be either string (inserted as-is) or
     * array (passed to getTag recursively)
     * getTag('a', ['href' => 'foo.html'], [['b', 'click here'], ' for fun'])
     * --> <a href="foo.html"><b>click here</b> for fun</a>
     *
     * 10. extended example:
     * getTag('a', ['href' => 'hello'], [
     *    ['b', 'class' => 'red', [
     *        ['i', 'class' => 'blue', 'welcome']
     *    ]]
     * ])
     * --> <a href="hello"><b class="red"><i class="blue">welcome</i></b></a>'
     *
     * @param array<0|string, string|bool>                                                                              $attr
     * @param string|array<int, array{0?: string, 1?: array<0|string, string|bool>, 2?: string|array|null}|string>|null $value
     */
    public function getTag(string $tag = null, array $attr = [], $value = null): string
    {
        $tag = strtolower($tag === null ? 'div' : $tag);
        $tagOrig = $tag;

        $isOpening = true;
        $isClosing = false;
        if (substr($tag, 0, 1) === '/') {
            $tag = substr($tag, 1);
            $isOpening = false;
            $isClosing = true;
        } elseif (substr($tag, -1) === '/') {
            $tag = substr($tag, 0, -1);
            $isClosing = true;
        }

        $isVoid = $this->isVoidTag($tag);
        if ($isVoid
            ? $isOpening && !$isClosing || !$isOpening || $value !== null
            : $isOpening && $isClosing
        ) {
            throw (new Exception('Wrong void tag usage'))
                ->addMoreInfo('tag', $tagOrig)
                ->addMoreInfo('isVoid', $isVoid);
        }

        if (isset($attr[0])) {
            if ($isClosing) {
                if ($isOpening) {
                    $tag = $attr[0] . '/';
                } else {
                    $tag = '/' . $attr[0];
                }
            } else {
                $tag = $attr[0];
            }
            unset($attr[0]);

            return $this->getTag($tag, $attr, $value);
        }

        if ($value !== null) {
            $result = [];
            foreach (is_scalar($value) ? [$value] : $value as $v) {
                if (is_array($v)) {
                    $result[] = $this->getTag(...$v);
                } elseif (['script' => true, 'style' => true][$tag] ?? false) {
                    if ($tag === 'script' && $v !== '') {
                        $result[] = '\'use strict\'; ';
                    }
                    // see https://mathiasbynens.be/notes/etago
                    $result[] = preg_replace('~(?<=<)(?=/\s*' . preg_quote($tag, '~') . '|!--)~', '\\\\', $v);
                } elseif (is_array($value)) { // todo, remove later and fix wrong usages, this is the original behaviour, only directly passed strings were escaped
                    $result[] = $v;
                } else {
                    $result[] = $this->encodeHtml($v);
                }
            }

            $value = implode('', $result);
        }

        $tmp = [];
        foreach ($attr as $key => $val) {
            if ($val === false) {
                continue;
            }

            if ($val === true) {
                $val = $key;
            }

            $val = (string) $val;
            $tmp[] = $key . '="' . $this->encodeHtml($val) . '"';
        }

        if ($isClosing && !$isOpening) {
            return '</' . $tag . '>';
        }

        return '<' . $tag . ($tmp !== [] ? ' ' . implode(' ', $tmp) : '') . ($isClosing && !$isVoid ? ' /' : '') . '>'
            . ($value !== null ? $value . '</' . $tag . '>' : '');
    }

    /**
     * Encodes string - convert all applicable chars to HTML entities.
     */
    public function encodeHtml(string $value): string
    {
        return htmlspecialchars($value, \ENT_HTML5 | \ENT_QUOTES | \ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * @return mixed
     */
    public function decodeJson(string $json)
    {
        $data = json_decode($json, true, 512, \JSON_BIGINT_AS_STRING | \JSON_THROW_ON_ERROR);

        return $data;
    }

    /**
     * @param mixed $data
     */
    public function encodeJson($data, bool $forceObject = false): string
    {
        $options = \JSON_UNESCAPED_SLASHES | \JSON_PRESERVE_ZERO_FRACTION | \JSON_UNESCAPED_UNICODE | \JSON_PRETTY_PRINT;
        if ($forceObject) {
            $options |= \JSON_FORCE_OBJECT;
        }

        $json = json_encode($data, $options | \JSON_THROW_ON_ERROR, 512);

        // IMPORTANT: always convert large integers to string, otherwise numbers can be rounded by JS
        // replace large JSON integers only, do not replace anything in JSON/JS strings
        $json = preg_replace_callback('~"(?:[^"\\\\]+|\\\\.)*+"\K|\'(?:[^\'\\\\]+|\\\\.)*+\'\K'
            . '|(?:^|[{\[,:])[ \n\r\t]*\K-?[1-9]\d{15,}(?=[ \n\r\t]*(?:$|[}\],:]))~s', function ($matches) {
                if ($matches[0] === '' || abs((int) $matches[0]) < (2 ** 53)) {
                    return $matches[0];
                }

                return '"' . $matches[0] . '"';
            }, $json);

        return $json;
    }

    /**
     * Return exception message using HTML block and Fomantic-UI formatting. It's your job
     * to put it inside boilerplate HTML and output, e.g:.
     *
     *   $app = new App();
     *   $app->initLayout([Layout\Centered::class]);
     *   $app->layout->template->dangerouslySetHtml('Content', $e->getHtml());
     *   $app->run();
     *   $app->callBeforeExit();
     */
    public function renderExceptionHtml(\Throwable $exception): string
    {
        return (string) new ExceptionRenderer\Html($exception);
    }

    protected function setupAlwaysRun(): void
    {
        register_shutdown_function(
            function () {
                if (!$this->runCalled) {
                    try {
                        $this->run();
                    } catch (ExitApplicationError $e) {
                        // let the process go and stop on ->callExit below
                    } catch (\Throwable $e) {
                        // process is already in shutdown
                        // must be forced to catch exception
                        $this->caughtException($e);
                    }

                    // call with true to trigger beforeExit event
                    $this->callBeforeExit();
                }
            }
        );
    }

    // RESPONSES

    /**
     * This can be overridden for future PSR-7 implementation.
     *
     * @param array<string, string> $headersNew
     *
     * @internal should be called only from self::outputResponse()
     */
    protected function outputResponseUnsafe(string $data, array $headersNew): void
    {
        $isCli = \PHP_SAPI === 'cli'; // for phpunit

        if (!headers_sent() || $isCli) {
            foreach ($headersNew as $k => $v) {
                if (!$isCli) {
                    if ($k === self::HEADER_STATUS_CODE) {
                        http_response_code($v === (string) (int) $v ? (int) $v : 500);
                    } else {
                        $kCamelCase = preg_replace_callback('~(?<![a-zA-Z])[a-z]~', function ($matches) {
                            return strtoupper($matches[0]);
                        }, $k);

                        header($kCamelCase . ': ' . $v);
                    }
                }
            }
        }

        echo $data;
    }

    /** @var array<string, string> */
    private static array $_sentHeaders = [];

    /**
     * Output Response to the client.
     *
     * @param array<string, string> $headers
     */
    protected function outputResponse(string $data, array $headers): void
    {
        $this->responseHeaders = $this->normalizeHeaders($this->responseHeaders);
        $headersAll = array_merge($this->responseHeaders, $this->normalizeHeaders($headers));
        unset($headers);
        $headersNew = array_diff_assoc($headersAll, self::$_sentHeaders);
        unset($headersAll);

        foreach (ob_get_status(true) as $status) {
            if ($status['buffer_used'] !== 0) {
                $lateError = new LateOutputError('Unexpected output detected');
                if ($this->catchExceptions) {
                    $this->caughtException($lateError);
                    $this->outputLateOutputError($lateError);
                }

                throw $lateError;
            }
        }

        $isCli = \PHP_SAPI === 'cli'; // for phpunit

        if (count($headersNew) > 0 && headers_sent() && !$isCli) {
            $lateError = new LateOutputError('Headers already sent, more headers cannot be set at this stage');
            if ($this->catchExceptions) {
                $this->caughtException($lateError);
                $this->outputLateOutputError($lateError);
            }

            throw $lateError;
        }

        foreach ($headersNew as $k => $v) {
            self::$_sentHeaders[$k] = $v;
        }

        $this->outputResponseUnsafe($data, $headersNew);
    }

    /**
     * @return never
     */
    protected function outputLateOutputError(LateOutputError $exception): void
    {
        $plainTextMessage = "\n" . '!! FATAL UI ERROR: ' . $exception->getMessage() . ' !!' . "\n";

        $headersAll = $this->normalizeHeaders(['content-type' => 'text/plain', self::HEADER_STATUS_CODE => '500']);
        $headersNew = array_diff_assoc($headersAll, self::$_sentHeaders);
        unset($headersAll);

        foreach ($headersNew as $k => $v) {
            self::$_sentHeaders[$k] = $v;
        }

        $this->outputResponseUnsafe($plainTextMessage, $headersNew);

        $this->runCalled = true; // prevent shutdown function from triggering

        exit(1); // should be never reached from phpunit because we set catchExceptions = false
    }

    /**
     * Output HTML response to the client.
     *
     * @param array<string, string> $headers
     */
    private function outputResponseHtml(string $data, array $headers = []): void
    {
        $this->outputResponse(
            $data,
            array_merge($this->normalizeHeaders($headers), ['content-type' => 'text/html'])
        );
    }

    /**
     * Output JSON response to the client.
     *
     * @param string|array          $data
     * @param array<string, string> $headers
     */
    private function outputResponseJson($data, array $headers = []): void
    {
        if (!is_string($data)) {
            $data = $this->encodeJson($data);
        }

        $this->outputResponse(
            $data,
            array_merge($this->normalizeHeaders($headers), ['content-type' => 'application/json'])
        );
    }

    /**
     * Generated html and js for portal view registered to app.
     */
    private function getRenderedPortals(): array
    {
        // prevent looping (calling App::terminateJson() recursively) if JsReload is used in Modal
        unset($_GET['__atk_reload']);

        $portals = [];
        foreach ($this->portals as $view) {
            $portals[$view->name]['html'] = $view->getHtml();
            $portals[$view->name]['js'] = $view->getJsRenderActions();
        }

        return $portals;
    }
}
