<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Ui\Js\Jquery;
use Atk4\Ui\Js\JsBlock;
use Atk4\Ui\Js\JsChain;
use Atk4\Ui\Js\JsExpression;
use Atk4\Ui\Js\JsExpressionable;

class JsCallback extends Callback
{
    /** @var array<string, string|JsExpressionable> Holds information about arguments passed in to the callback. */
    public $args = [];

    /** @var string Text to display as a confirmation. Set with setConfirm(..). */
    public $confirm;

    /** @var array|null Use this apiConfig variable to pass API settings to Fomantic-UI in .api(). */
    public $apiConfig;

    /** @var string|null Include web storage data item (key) value to be included in the request. */
    public $storeName;

    /**
     * Usually JsCallback should not allow to trigger during a reload.
     * Consider reloading a form, if triggering is allowed during the reload process
     * then $form->model could be saved during that reload which can lead to unexpected result
     * if model ID is not properly handled.
     *
     * @var bool
     */
    public $triggerOnReload = false;

    public function jsExecute(): JsBlock
    {
        $this->assertIsInitialized();

        return new JsBlock([(new Jquery($this->getOwner() /* TODO element and loader element should be passed explicitly */))->atkAjaxec([
            'url' => $this->getJsUrl(),
            'urlOptions' => $this->args,
            'confirm' => $this->confirm,
            'apiConfig' => $this->apiConfig,
            'storeName' => $this->storeName,
        ])]);
    }

    /**
     * Set a confirmation to be displayed before actually sending a request.
     *
     * @param string $text
     */
    public function setConfirm($text = 'Are you sure?'): void
    {
        $this->confirm = $text;
    }

    /**
     * @param \Closure(Jquery, mixed, mixed, mixed, mixed, mixed, mixed, mixed, mixed, mixed, mixed): (JsExpressionable|View|string|void) $fx
     *
     * @return $this
     */
    public function set($fx = null, $args = null)
    {
        if (!$fx instanceof \Closure) {
            throw new \TypeError('$fx must be of type Closure');
        }

        $this->args = [];
        foreach ($args ?? [] as $key => $val) {
            if (is_int($key)) {
                $key = $this->name . '_c' . $key;
            }
            $this->args[$key] = $val;
        }

        parent::set(function () use ($fx) {
            $chain = new Jquery();

            $values = [];
            foreach (array_keys($this->args) as $key) {
                $values[] = $this->getApp()->getRequestPostParam($key);
            }

            $response = $fx($chain, ...$values);

            if (count($chain->_chain) === 0) {
                // TODO should we create/pass $chain to $fx at all?
                $chain = null;
            } elseif ($response) {
                // TODO throw when non-empty chain is to be ignored?
            }

            $ajaxec = $response ? $this->getAjaxec($response, $chain) : null;

            $this->terminateAjax($ajaxec);
        });

        return $this;
    }

    /**
     * A proper way to finish execution of AJAX response. Generates JSON
     * which is returned to frontend.
     *
     * @param string|null                        $ajaxec
     * @param ($success is true ? null : string) $msg     General message, typically won't be displayed
     * @param bool                               $success Was request successful or not
     */
    public function terminateAjax($ajaxec, $msg = null, bool $success = true): void
    {
        $data = ['success' => $success];
        if (!$success) {
            $data['message'] = $msg;
        }
        $data['atkjs'] = $ajaxec;

        if ($this->canTerminate()) {
            $this->getApp()->terminateJson($data);
        }
    }

    /**
     * Provided with a $response from callbacks convert it into a JavaScript code.
     *
     * @param JsExpressionable|View|string|null $response response from callbacks,
     * @param JsChain                           $chain
     */
    public function getAjaxec($response, $chain = null): string
    {
        $jsBlock = new JsBlock();
        if ($chain !== null) {
            $jsBlock->addStatement($chain);
        }
        $jsBlock->addStatement($this->_getProperAction($response));

        return $jsBlock->jsRender();
    }

    public function getUrl(string $mode = 'callback'): string
    {
        throw new Exception('Do not use getUrl on JsCallback, use getJsUrl()');
    }

    /**
     * Transform response into proper JS Action and return it.
     *
     * @param View|string|JsExpressionable $response
     */
    private function _getProperAction($response): JsExpressionable
    {
        if ($response instanceof View) {
            $response = $this->_jsRenderIntoModal($response);
        } elseif (is_string($response)) { // TODO alert() should be removed
            $response = new JsExpression('alert([])', [$response]);
        }

        return $response;
    }

    private function _jsRenderIntoModal(View $response): JsExpressionable
    {
        if ($response instanceof Modal) {
            $html = $response->getHtml();
        } else {
            $modal = new Modal(['name' => false]);
            $modal->setApp($this->getApp());
            $modal->add($response);
            $html = $modal->getHtml();
        }

        return new JsExpression('$([html]).modal(\'show\').data(\'needRemove\', true).addClass(\'atk-callback-response\')', ['html' => $html]);
    }
}
