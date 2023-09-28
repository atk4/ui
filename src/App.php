<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Core\AppScopeTrait;
use Atk4\Core\DiContainerTrait;
use Atk4\Core\DynamicMethodTrait;
use Atk4\Core\ExceptionRenderer;
use Atk4\Core\Factory;
use Atk4\Core\HookTrait;
use Atk4\Core\TraitUtil;
use Atk4\Core\WarnDynamicPropertyTrait;
use Atk4\Data\Persistence;
use Atk4\Ui\Exception\ExitApplicationError;
use Atk4\Ui\Exception\LateOutputError;
use Atk4\Ui\Exception\UnhandledCallbackExceptionError;
use Atk4\Ui\Js\JsExpression;
use Atk4\Ui\Js\JsExpressionable;
use Atk4\Ui\Persistence\Ui as UiPersistence;
use Atk4\Ui\UserAction\ExecutorFactory;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use Nyholm\Psr7Server\ServerRequestCreator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Log\LoggerInterface;

class App
{
    use AppScopeTrait;
    use DiContainerTrait;
    use DynamicMethodTrait;
    use HookTrait;

    public const HOOK_BEFORE_EXIT = self::class . '@beforeExit';
    public const HOOK_BEFORE_RENDER = self::class . '@beforeRender';

    /** @var array|false Location where to load JS/CSS files */
    public $cdn = [
        'atk' => '/public',
        'jquery' => '/public/external/jquery/dist',
        'fomantic-ui' => '/public/external/fomantic-ui/dist',
        'flatpickr' => '/public/external/flatpickr/dist',
        'highlight.js' => '/public/external/@highlightjs/cdn-assets',
        'chart.js' => '/public/external/chart.js/dist', // for atk4/chart
    ];

    /** @var ExecutorFactory App wide executor factory object for Model user action. */
    protected $executorFactory;

    /**
     * @var string Version of Agile UI
     *
     * @TODO remove, no longer needed for CDN versioning as we bundle all resources
     */
    public $version = '5.0-dev';

    /** @var string Name of application */
    public $title = 'Agile UI - Untitled Application';

    /** @var Layout the top-most view object */
    public $layout;

    /** @var string|array Set one or more directories where templates should reside. */
    public $templateDir;

    /** @var bool Will replace an exception handler with our own, that will output errors nicely. */
    public $catchExceptions = true;

    /** Will display error if callback wasn't triggered. */
    protected bool $catchRunawayCallbacks = true;

    /** Will always run application even if developer didn't explicitly executed run();. */
    protected bool $alwaysRun = true;

    /** Will be set to true after app->run() is called, which may be done automatically on exit. */
    public bool $runCalled = false;

    /** Will be set to true after exit is called. */
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

    private ServerRequestInterface $request;

    private ResponseInterface $response;

    /**
     * If filename path part is missing during building of URL, this page will be used.
     * Set to empty string when when your webserver supports index.php autoindex or you use mod_rewrite with routing.
     *
     * @internal only for self::url() method
     */
    protected string $urlBuildingIndexPage = 'index';

    /**
     * Remove and re-add the extension of the file during parsing requests and building URL.
     *
     * @internal only for self::url() method
     */
    protected string $urlBuildingExt = '.php';

    /** @var bool Call exit in place of throw Exception when Application need to exit. */
    public $callExit = true;

    /** @var array<string, bool> global sticky arguments */
    protected array $stickyGetArguments = [
        '__atk_json' => false,
        '__atk_tab' => false,
    ];

    /** @var class-string */
    public $templateClass = HtmlTemplate::class;

    public function __construct(array $defaults = [])
    {
        if (isset($defaults['request'])) {
            $this->request = $defaults['request'];
            unset($defaults['request']);
        } else {
            $requestFactory = new Psr17Factory();
            $requestCreator = new ServerRequestCreator($requestFactory, $requestFactory, $requestFactory, $requestFactory);

            $noGlobals = [];
            foreach (['_GET', '_COOKIE', '_FILES'] as $k) {
                if (!array_key_exists($k, $GLOBALS)) {
                    $noGlobals[] = $k;
                    $GLOBALS[$k] = [];
                }
            }
            try {
                $this->request = $requestCreator->fromGlobals();
            } finally {
                foreach ($noGlobals as $k) {
                    unset($GLOBALS[$k]);
                }
            }
        }

        if (isset($defaults['response'])) {
            $this->response = $defaults['response'];
            unset($defaults['response']);
        } else {
            $this->response = new Response();
        }

        // disable caching by default
        $this->setResponseHeader('Cache-Control', 'no-store');

        $this->setApp($this);

        $this->setDefaults($defaults);

        $this->setupTemplateDirs();

        foreach ($this->cdn as $k => $v) {
            if (str_starts_with($v, '/') && !str_starts_with($v, '//')) {
                $this->cdn[$k] = $this->createRequestPathFromLocalPath(__DIR__ . '/..' . $v);
            }
        }

        // set our exception handler
        if ($this->catchExceptions) {
            set_exception_handler(\Closure::fromCallable([$this, 'caughtException']));
            set_error_handler(static function (int $severity, string $msg, string $file, int $line): bool {
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
            });
            http_response_code(500);
        }

        // always run app on shutdown
        if ($this->alwaysRun) {
            $this->setupAlwaysRun();
        }

        if ($this->uiPersistence === null) {
            $this->uiPersistence = new UiPersistence();
        }

        if (!str_starts_with($this->getRequest()->getUri()->getPath(), '/')) {
            throw (new Exception('Request URL path must always start with \'/\''))
                ->addMoreInfo('url', (string) $this->getRequest()->getUri());
        }

        if ($this->session === null) {
            $this->session = new App\SessionManager();
        }

        // setting up default executor factory
        $this->executorFactory = Factory::factory([ExecutorFactory::class]);
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

        if (($this->isJsUrlRequest() || $this->getRequest()->getHeaderLine('X-Requested-With') === 'XMLHttpRequest')
                && !$this->hasRequestQueryParam('__atk_tab')) {
            $this->outputResponseJson([
                'success' => false,
                'message' => $this->layout->getHtml(),
            ]);
        } else {
            $this->setResponseStatusCode(500);
            $this->run();
        }

        // process is already in shutdown because of uncaught exception
        // no need of call exit function
        $this->callBeforeExit();
    }

    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }

    /**
     * Check if a specific GET parameter exists in the HTTP request.
     */
    public function hasRequestQueryParam(string $key): bool
    {
        return $this->tryGetRequestQueryParam($key) !== null;
    }

    /**
     * Try to get the value of a specific GET parameter from the HTTP request.
     */
    public function tryGetRequestQueryParam(string $key): ?string
    {
        return $this->getRequest()->getQueryParams()[$key] ?? null;
    }

    /**
     * Get the value of a specific GET parameter from the HTTP request.
     */
    public function getRequestQueryParam(string $key): string
    {
        $res = $this->tryGetRequestQueryParam($key);
        if ($res === null) {
            throw (new Exception('GET param does not exist'))
                ->addMoreInfo('key', $key);
        }

        return $res;
    }

    /**
     * Check if a specific POST parameter exists in the HTTP request.
     */
    public function hasRequestPostParam(string $key): bool
    {
        return $this->tryGetRequestPostParam($key) !== null;
    }

    /**
     * Try to get the value of a specific POST parameter from the HTTP request.
     */
    public function tryGetRequestPostParam(string $key): ?string
    {
        return $this->getRequest()->getParsedBody()[$key] ?? null;
    }

    /**
     * Get the value of a specific POST parameter from the HTTP request.
     *
     * @return mixed
     */
    public function getRequestPostParam(string $key)
    {
        $res = $this->tryGetRequestPostParam($key);
        if ($res === null) {
            throw (new Exception('POST param does not exist'))
                ->addMoreInfo('key', $key);
        }

        return $res;
    }

    /**
     * Check if a specific uploaded file exists in the HTTP request.
     */
    public function hasRequestUploadedFile(string $key): bool
    {
        return $this->tryGetRequestUploadedFile($key) !== null;
    }

    /**
     * Try to get a specific uploaded file from the HTTP request.
     */
    public function tryGetRequestUploadedFile(string $key): ?UploadedFileInterface
    {
        return $this->getRequest()->getUploadedFiles()[$key] ?? null;
    }

    /**
     * Get a specific uploaded file from the HTTP request.
     */
    public function getRequestUploadedFile(string $key): UploadedFileInterface
    {
        $res = $this->tryGetRequestUploadedFile($key);
        if ($res === null) {
            throw (new Exception('FILES upload does not exist'))
                ->addMoreInfo('key', $key);
        }

        return $res;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    protected function assertHeadersNotSent(): void
    {
        if (headers_sent()
            && \PHP_SAPI !== 'cli' // for phpunit
            && $this->response->getHeaderLine('Content-Type') !== 'text/event-stream' // for SSE
        ) {
            $lateError = new LateOutputError('Headers already sent, more headers cannot be set at this stage');
            if ($this->catchExceptions) {
                $this->caughtException($lateError);
                $this->outputLateOutputError($lateError);
            }

            throw $lateError;
        }
    }

    /**
     * @return $this
     */
    public function setResponseStatusCode(int $statusCode): self
    {
        $this->assertHeadersNotSent();

        $this->response = $this->response->withStatus($statusCode);

        return $this;
    }

    /**
     * @return $this
     */
    public function setResponseHeader(string $name, string $value): self
    {
        $this->assertHeadersNotSent();

        if ($value === '') {
            $this->response = $this->response->withoutHeader($name);
        } else {
            $name = preg_replace_callback('~(?<![a-zA-Z])[a-z]~', static function ($matches) {
                return strtoupper($matches[0]);
            }, strtolower($name));

            $this->response = $this->response->withHeader($name, $value);
        }

        return $this;
    }

    /**
     * Will perform a preemptive output and terminate. Do not use this
     * directly, instead call it form Callback, JsCallback or similar
     * other classes.
     *
     * @param string|StreamInterface|array $output Array type is supported only for JSON response
     *
     * @return never
     */
    public function terminate($output = ''): void
    {
        $type = preg_replace('~;.*~', '', strtolower($this->response->getHeaderLine('Content-Type'))); // in LC without charset
        if ($type === '') {
            throw new Exception('Content type must be always set');
        }

        if ($output instanceof StreamInterface) {
            $this->response = $this->response->withBody($output);
            $this->outputResponse('');
        } elseif ($type === 'application/json') {
            if (is_string($output)) {
                $output = $this->decodeJson($output);
            }

            $this->outputResponseJson($output);
        } elseif ($this->hasRequestQueryParam('__atk_tab') && $type === 'text/html') {
            $output = $this->getTag('script', [], '$(function () {' . $output['atkjs'] . '});')
                . $output['html'];

            $this->outputResponseHtml($output);
        } elseif ($type === 'text/html') {
            $this->outputResponseHtml($output);
        } else {
            $this->outputResponse($output);
        }

        $this->runCalled = true; // prevent shutdown function from triggering
        $this->callExit();
    }

    /**
     * @param string|array|View|HtmlTemplate $output
     *
     * @return never
     */
    public function terminateHtml($output): void
    {
        if ($output instanceof View) {
            $output = $output->render();
        } elseif ($output instanceof HtmlTemplate) {
            $output = $output->renderToHtml();
        }

        $this->setResponseHeader('Content-Type', 'text/html');
        $this->terminate($output);
    }

    /**
     * @param string|array|View $output
     *
     * @return never
     */
    public function terminateJson($output): void
    {
        if ($output instanceof View) {
            $output = $output->renderToJsonArr();
        }

        $this->setResponseHeader('Content-Type', 'application/json');
        $this->terminate($output);
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
        $minified = !file_exists(__DIR__ . '/../.git');

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

        // set JS bundle dynamic loading path
        $this->html->template->dangerouslySetHtml(
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

            if ($this->hasRequestQueryParam(Callback::URL_QUERY_TARGET) && $this->catchRunawayCallbacks) {
                throw (new Exception('Callback requested, but never reached. You may be missing some arguments in request URL.'))
                    ->addMoreInfo('callback', $this->getRequestQueryParam(Callback::URL_QUERY_TARGET));
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
     * Load template by template file name.
     *
     * @return HtmlTemplate
     */
    public function loadTemplate(string $filename)
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

    protected function createRequestPathFromLocalPath(string $localPath): string
    {
        // $localPath does not need realpath() as the path is expected to be built using __DIR__
        // which has symlinks resolved

        static $requestUrlPath = null;
        static $requestLocalPath = null;
        if ($requestUrlPath === null) {
            if (\PHP_SAPI === 'cli') { // for phpunit
                $requestUrlPath = '/';
                $requestLocalPath = \Closure::bind(static function () {
                    return (new ExceptionRenderer\Html(new \Exception()))->getVendorDirectory();
                }, null, ExceptionRenderer\Html::class)();
            } else {
                $request = new \Symfony\Component\HttpFoundation\Request([], [], [], [], [], $_SERVER);
                $requestUrlPath = $request->getBasePath();
                $requestLocalPath = realpath($request->server->get('SCRIPT_FILENAME'));
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

        return $this->tryGetRequestQueryParam($name);
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
     * @param string|array<0|string, string|int|false> $page                URL as string or array with page path as first element and other GET arguments
     * @param array<string, string>                    $extraRequestUrlArgs additional URL arguments, deleting sticky can delete them
     */
    public function url($page = [], array $extraRequestUrlArgs = []): string
    {
        if (is_string($page)) {
            $pageExploded = explode('?', $page, 2);
            parse_str($pageExploded[1] ?? '', $page);
            $pagePath = $pageExploded[0] !== '' ? $pageExploded[0] : null;
        } else {
            $pagePath = $page[0] ?? null;
            unset($page[0]);
        }

        $request = $this->getRequest();

        if ($pagePath === null) {
            $pagePath = $request->getUri()->getPath();
        }
        if (str_ends_with($pagePath, '/')) {
            $pagePath .= $this->urlBuildingIndexPage;
        }
        if (!str_ends_with($pagePath, '/') && !str_contains(basename($pagePath), '.')) {
            $pagePath .= $this->urlBuildingExt;
        }

        $args = $extraRequestUrlArgs;

        // add sticky arguments
        $requestQueryParams = $request->getQueryParams();
        foreach ($this->stickyGetArguments as $k => $v) {
            if ($v && isset($requestQueryParams[$k])) {
                $args[$k] = $requestQueryParams[$k];
            } else {
                unset($args[$k]);
            }
        }

        // add arguments
        foreach ($page as $k => $v) {
            if ($v === false) {
                unset($args[$k]);
            } else {
                $args[$k] = $v;
            }
        }

        $pageQuery = http_build_query($args, '', '&', \PHP_QUERY_RFC3986);

        return $pagePath . ($pageQuery !== '' ? '?' . $pageQuery : '');
    }

    /**
     * Build a URL that application can use for JS callbacks. Some framework integration will use a different routing
     * mechanism for non-HTML response.
     *
     * @param string|array<0|string, string|int|false> $page                URL as string or array with page path as first element and other GET arguments
     * @param array<string, string>                    $extraRequestUrlArgs additional URL arguments, deleting sticky can delete them
     */
    public function jsUrl($page = [], array $extraRequestUrlArgs = []): string
    {
        // append to the end but allow override
        $extraRequestUrlArgs = array_merge($extraRequestUrlArgs, ['__atk_json' => 1], $extraRequestUrlArgs);

        return $this->url($page, $extraRequestUrlArgs);
    }

    /**
     * Request was made using App::jsUrl().
     */
    public function isJsUrlRequest(): bool
    {
        return $this->hasRequestQueryParam('__atk_json') && $this->getRequestQueryParam('__atk_json') !== '0';
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
     * @param string|array<0|string, string|int|false> $page
     */
    public function redirect($page, bool $permanent = false): void
    {
        $this->setResponseStatusCode($permanent ? 301 : 302);
        $this->setResponseHeader('location', $this->url($page));
        $this->terminateHtml('');
    }

    /**
     * Generate action for redirecting user to another page.
     *
     * @param string|array<0|string, string|int|false> $page
     */
    public function jsRedirect($page, bool $newWindow = false): JsExpressionable
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
     * @param array<0|string, string|bool>                                                                             $attr
     * @param string|array<int, array{0: string, 1?: array<0|string, string|bool>, 2?: string|array|null}|string>|null $value
     */
    public function getTag(string $tag, array $attr = [], $value = null): string
    {
        $tag = strtolower($tag);
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
        if (is_array($data) || is_object($data)) {
            $checkNoObjectFx = static function ($v) {
                if (is_object($v)) {
                    throw (new Exception('Object to JSON encode is not supported'))
                        ->addMoreInfo('value', $v);
                }
            };

            if (is_object($data)) {
                $checkNoObjectFx($data);
            } else {
                array_walk_recursive($data, $checkNoObjectFx);
            }
        }

        $options = \JSON_UNESCAPED_SLASHES | \JSON_PRESERVE_ZERO_FRACTION | \JSON_UNESCAPED_UNICODE | \JSON_PRETTY_PRINT;
        if ($forceObject) {
            $options |= \JSON_FORCE_OBJECT;
        }

        $json = json_encode($data, $options | \JSON_THROW_ON_ERROR, 512);

        // IMPORTANT: always convert large integers to string, otherwise numbers can be rounded by JS
        // replace large JSON integers only, do not replace anything in JSON/JS strings
        $json = preg_replace_callback('~"(?:[^"\\\\]+|\\\\.)*+"\K|\'(?:[^\'\\\\]+|\\\\.)*+\'\K'
            . '|(?:^|[{\[,:])[ \n\r\t]*\K-?[1-9]\d{15,}(?=[ \n\r\t]*(?:$|[}\],:]))~s', static function ($matches) {
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
                        // set_exception_handler does not work in shutdown
                        // https://github.com/php/php-src/issues/10695
                        $this->caughtException($e);
                    }

                    // call with true to trigger beforeExit event
                    $this->callBeforeExit();
                }
            }
        );
    }

    /**
     * @internal should be called only from self::outputResponse() and self::outputLateOutputError()
     */
    protected function emitResponse(): void
    {
        if (!headers_sent() || $this->response->getHeaders() !== []) { // avoid throwing late error in loop
            http_response_code($this->response->getStatusCode());
        }

        foreach ($this->response->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header($name . ': ' . $value, false);
            }
        }

        $stream = $this->response->getBody();
        if ($stream->isSeekable()) {
            $stream->rewind();
        }

        // for streaming response
        if (!$stream->isReadable()) {
            return;
        }

        while (!$stream->eof()) {
            echo $stream->read(16 * 1024);
        }
    }

    protected function outputResponse(string $data): void
    {
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

        $this->assertHeadersNotSent();

        // TODO hack for SSE
        // https://github.com/atk4/ui/pull/1706#discussion_r757819527
        if (headers_sent() && $this->response->getHeaderLine('Content-Type') === 'text/event-stream') {
            echo $data;

            return;
        }

        if ($data !== '') {
            $this->response->getBody()->write($data);
        }

        $this->emitResponse();
    }

    /**
     * @return never
     */
    protected function outputLateOutputError(LateOutputError $exception): void
    {
        $this->response = $this->response->withStatus(500);

        // late error means headers were already sent to the client, so remove all response headers,
        // to avoid throwing late error in loop
        foreach (array_keys($this->response->getHeaders()) as $name) {
            $this->response = $this->response->withoutHeader($name);
        }

        $this->response = $this->response->withBody((new Psr17Factory())->createStream("\n"
            . '!! FATAL UI ERROR: ' . $exception->getMessage() . ' !!'
            . "\n"));
        $this->emitResponse();

        $this->runCalled = true; // prevent shutdown function from triggering

        exit(1); // should be never reached from phpunit because we set catchExceptions = false
    }

    /**
     * Output HTML response to the client.
     */
    private function outputResponseHtml(string $data): void
    {
        $this->setResponseHeader('Content-Type', 'text/html');
        $this->outputResponse($data);
    }

    /**
     * @param string|array $data
     */
    private function outputResponseJson($data): void
    {
        if (!is_string($data)) {
            $data = $this->encodeJson($data);
        }

        $this->setResponseHeader('Content-Type', 'application/json');
        $this->outputResponse($data);
    }
}
