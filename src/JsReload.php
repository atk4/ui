<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Core\WarnDynamicPropertyTrait;

/**
 * This class generates action, that will be able to loop-back to the callback method.
 */
class JsReload implements JsExpressionable
{
    use WarnDynamicPropertyTrait;

    /** @var View Specifies which view to reload. Use constructor to set. */
    public $view;

    /** @var JsExpression|null A Js function to execute after reload is complete and onSuccess is execute. */
    public $afterSuccess;

    /**
     * If defined, they will be added at the end of your URL.
     * Value in ARG can be either string or JsExpressionable.
     */
    public array $args = [];

    /**
     * Fomantic-UI api settings.
     * ex: ['loadingDuration' => 1000].
     */
    public array $apiConfig = [];

    /** @var bool */
    public $includeStorage = false;

    public function __construct(View $view, array $args = [], JsExpression $afterSuccess = null, array $apiConfig = [], bool $includeStorage = false)
    {
        $this->view = $view;
        $this->args = $args;
        $this->afterSuccess = $afterSuccess;
        $this->apiConfig = $apiConfig;
        $this->includeStorage = $includeStorage;
    }

    public function jsRender(): string
    {
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
