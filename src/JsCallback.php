<?php

declare(strict_types=1);

namespace Atk4\Ui;

class JsCallback extends Callback implements JsExpressionable
{
    /** @var array Holds information about arguments passed in to the callback. */
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
     * if model id is not properly handled.
     *
     * @var bool
     */
    public $triggerOnReload = false;

    /**
     * When multiple JsExpressionable's are collected inside an array and may
     * have some degree of nesting, convert it into a one-dimensional array,
     * so that it's easier for us to wrap it into a function body.
     */
    public function flatternArray(array $response): array
    {
        $res = [];
        foreach ($response as $element) {
            if (is_array($element)) {
                $res = array_merge($res, $this->flatternArray($element));
            } else {
                $res[] = $element;
            }
        }

        return $res;
    }

    public function jsRender(): string
    {
        $this->getApp(); // assert has App

        return (new Jquery())->atkAjaxec([
            'url' => $this->getJsUrl(),
            'urlOptions' => $this->args,
            'confirm' => $this->confirm,
            'apiConfig' => $this->apiConfig,
            'storeName' => $this->storeName,
        ])->jsRender();
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

    public function set($fx = null, $args = null)
    {
        $this->args = [];
        foreach ($args ?? [] as $key => $val) {
            if (is_numeric($key)) {
                $key = 'c' . $key;
            }
            $this->args[$key] = $val;
        }

        parent::set(function () use ($fx) {
            $chain = new Jquery(new JsExpression('this'));

            $values = [];
            foreach ($this->args as $key => $value) {
                $values[] = $_POST[$key] ?? null;
            }

            $response = $fx($chain, ...$values);

            $ajaxec = $response ? $this->getAjaxec($response, $chain) : null;

            $this->terminateAjax($ajaxec);
        });

        return $this;
    }

    /**
     * A proper way to finish execution of AJAX response. Generates JSON
     * which is returned to frontend.
     *
     * @param string|null $ajaxec
     * @param ($success is true ? null : string)      $msg     General message, typically won't be displayed
     * @param bool $success Was request successful or not
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
     * @param array|JsExpressionable $response response from callbacks,
     * @param JsChain                $chain
     */
    public function getAjaxec($response, $chain = null): string
    {
        $actions = [];

        if ($chain && $chain->_chain) {
            $actions[] = $chain;
        }

        if (is_array($response)) {
            $response = $this->flatternArray($response);
            foreach ($response as $r) {
                if ($r === null) {
                    continue;
                }
                $actions[] = $this->_getProperAction($r);
            }
        } else {
            $actions[] = $this->_getProperAction($response);
        }

        $ajaxec = implode(";\n", array_map(function (JsExpressionable $r) {
            return $r->jsRender();
        }, $actions));

        return $ajaxec;
    }

    public function getUrl(string $mode = 'callback'): string
    {
        throw new Exception('Do not use getUrl on JsCallback, use getJsUrl()');
    }

    /**
     * Transform response into proper js Action and return it.
     *
     * @param View|string|JsExpressionable $response
     */
    private function _getProperAction($response): JsExpressionable
    {
        if ($response instanceof View) {
            $response = $this->_jsRenderIntoModal($response);
        } elseif (is_string($response)) {
            $response = new JsExpression('alert([])', [$response]);
        }

        return $response;
    }

    /**
     * Render View into modal.
     */
    private function _jsRenderIntoModal(View $response): JsExpressionable
    {
        if ($response instanceof Modal) {
            $html = $response->getHtml();
        } else {
            $modal = new Modal(['name' => false]);
            $modal->add($response);
            $html = $modal->getHtml();
        }

        return new JsExpression('$([html]).modal(\'show\').data(\'needRemove\', true).addClass(\'atk-callback-response\')', ['html' => $html]);
    }
}
