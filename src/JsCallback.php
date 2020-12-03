<?php

declare(strict_types=1);

namespace Atk4\Ui;

class JsCallback extends Callback implements JsExpressionable
{
    /**
     * Holds information about arguments passed in to the callback.
     *
     * @var array
     */
    public $args = [];

    /**
     * Text to display as a confirmation. Set with setConfirm(..).
     *
     * @var string
     */
    public $confirm;

    /**
     * Use this apiConfig variable to pass API settings to Semantic UI in .api().
     *
     * @var array|null
     */
    public $apiConfig;

    /**
     * Include web storage data item (key) value to be include in the request.
     *
     * @var string|null
     */
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
     *
     * @param array $response
     *
     * @return array
     */
    public function flatternArray($response)
    {
        if (!is_array($response)) {
            return [$response];
        }

        $out = [];

        foreach ($response as $element) {
            $out = array_merge($out, $this->flatternArray($element));
        }

        return $out;
    }

    public function jsRender(): string
    {
        $this->getApp(); // assert has App

        return (new Jquery())->atkAjaxec([
            'uri' => $this->getJsUrl(),
            'uri_options' => $this->args,
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
    public function setConfirm($text = 'Are you sure?')
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
            try {
                $chain = new Jquery(new JsExpression('this'));

                $values = [];
                foreach ($this->args as $key => $value) {
                    $values[] = $_POST[$key] ?? null;
                }

                $response = $fx(...array_merge([$chain], $values));

                $ajaxec = $response ? $this->getAjaxec($response, $chain) : null;

                $this->terminateAjax($ajaxec);
            } catch (\Atk4\Data\ValidationException $e) {
                // Validation exceptions will be presented to user in a friendly way
                $msg = new Message($e->getMessage());
                $msg->addClass('error');

                $this->terminateAjax(null, $msg->getHtml(), false);
            }
        });

        return $this;
    }

    /**
     * A proper way to finish execution of AJAX response. Generates JSON
     * which is returned to frontend.
     *
     * @param array|JsExpressionable $ajaxec  Array of JsExpressionable
     * @param string                 $msg     General message, typically won't be displayed
     * @param bool                   $success Was request successful or not
     */
    public function terminateAjax($ajaxec, $msg = null, $success = true)
    {
        if ($this->canTerminate()) {
            $this->getApp()->terminateJson(['success' => $success, 'message' => $msg, 'atkjs' => $ajaxec]);
        }
    }

    /**
     * Provided with a $response from callbacks convert it into a JavaScript code.
     *
     * @param array|JsExpressionable $response response from callbacks,
     * @param string                 $chain    JavaScript string
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
        $action = null;
        if ($response instanceof View) {
            $action = $this->_jsRenderIntoModal($response);
        } elseif (is_string($response)) {
            $action = new JsExpression('alert([])', [$response]);
        } elseif ($response instanceof JsExpressionable) {
            $action = $response;
        } else {
            throw (new Exception('Incorrect callback. Response must be of type JsExpressionable, View, or String.'))
                ->addMoreInfo('r', $response);
        }

        return $action;
    }

    /**
     * Render View into modal.
     */
    private function _jsRenderIntoModal(View $response): JsExpressionable
    {
        if ($response instanceof Modal) {
            $html = $response->getHtml();
        } else {
            $modal = new Modal(['id' => false]);
            $modal->add($response);
            $html = $modal->getHtml();
        }

        return new JsExpression('$([html]).modal("show").data("needRemove", true).addClass("atk-callback-response")', ['html' => $html]);
    }
}
