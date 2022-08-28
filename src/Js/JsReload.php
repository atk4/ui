<?php

declare(strict_types=1);

namespace Atk4\Ui\Js;

use Atk4\Core\WarnDynamicPropertyTrait;
use Atk4\Ui\View;

/**
 * Generates action, that will be able to loop-back to the callback method.
 */
class JsReload implements JsExpressionable
{
    use WarnDynamicPropertyTrait;

    /** Specifies which view to reload. Use constructor to set. */
    public View $view;

    /** @var JsExpressionable|null A JS function to execute after reload is complete and onSuccess is execute. */
    public $afterSuccess;

    /** @var array<string, string|int|JsExpressionable> Added at the end of your URL. */
    public array $args = [];

    /**
     * Fomantic-UI api settings.
     * ex: ['loadingDuration' => 1000].
     */
    public array $apiConfig = [];

    /** @var bool */
    public $includeStorage = false;

    /**
     * @param array<string, string|int|JsExpressionable> $args
     */
    public function __construct(View $view, array $args = [], JsExpressionable $afterSuccess = null, array $apiConfig = [], bool $includeStorage = false)
    {
        $this->view = $view;
        $this->args = $args;
        $this->afterSuccess = $afterSuccess;
        $this->apiConfig = $apiConfig;
        $this->includeStorage = $includeStorage;
    }

    public function dumpRenderTree(View $view, bool $rec = false): void
    {
        if ($view->issetOwner() && $view->getOwner() instanceof View) {
            $this->dumpRenderTree($view->getOwner(), true);
        }

        echo get_class($view);

        echo "\n" . (!$rec ? "\n\n" : '');
    }

    public function jsRender(): string
    {
        /*ini_set('output_buffering', (string) (1024 * 1024));
        ob_start();
        $this->dumpRenderTree($this->view);
        // test URL: /demos/interactive/modal.php?__atk_m=atk_layout_maestro_modal_5&__atk_cbtarget=atk_layout_maestro_modal_5_view_callbacklater&__atk_cb_atk_layout_maestro_modal_5_view_callbacklater=ajax&__atk_json=1
        $url = $this->view->jsUrl(['__atk_reload' => $this->view->name]);
        echo 'actual:   ';
        var_dump($url);
        echo 'expected: string(166) "modal.php?__atk_m=atk_layout_maestro_modal_5&__atk_cb_atk_layout_maestro_modal_5_view_callbacklater=ajax&__atk_reload=atk_layout_maestro_modal_5_view_demos_viewtester"' . "\n";
        ob_end_flush();

        exit;*/

        $final = (new Jquery($this->view))
            ->atkReloadView(
                [
                    'url' => $this->view->jsUrl(['__atk_reload' => $this->view->name]),
                    'urlOptions' => $this->args !== [] ? $this->args : null,
                    'afterSuccess' => $this->afterSuccess ? $this->afterSuccess->jsRender() : null,
                    'apiConfig' => $this->apiConfig !== [] ? $this->apiConfig : null,
                    'storeName' => $this->includeStorage ? $this->view->name : null,
                ]
            );

        return $final->jsRender();
    }
}
