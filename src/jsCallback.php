<?php

namespace atk4\ui;

class jsCallback extends Callback implements jsExpressionable
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
     * When multiple jsExpressionable's are collected inside an array and may
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

    public function jsRender()
    {
        if (!$this->app) {
            throw new Exception(['Call-back must be part of a RenderTree']);
        }

        return (new jQuery())->atkAjaxec([
            'uri' => $this->getJSURL(),
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

    public function set($callback, $args = [])
    {
        $this->args = [];

        foreach ($args as $key => $val) {
            if (is_numeric($key)) {
                $key = 'c' . $key;
            }
            $this->args[$key] = $val;
        }

        parent::set(function () use ($callback) {
            try {
                $chain = new jQuery(new jsExpression('this'));

                $values = [];
                foreach ($this->args as $key => $value) {
                    $values[] = $_POST[$key] ?? null;
                }

                $response = call_user_func_array($callback, array_merge([$chain], $values));

                $ajaxec = $response ? $this->getAjaxec($response, $chain) : null;

                $this->terminateAjax($ajaxec);
            } catch (\atk4\data\ValidationException $e) {
                // Validation exceptions will be presented to user in a friendly way
                $m = new Message($e->getMessage());
                $m->addClass('error');

                $this->terminateAjax(null, $m->getHTML(), false);
            }
        });

        return $this;
    }

    /**
     * A proper way to finish execution of AJAX response. Generates JSON
     * which is returned to frontend.
     *
     * @param array|jsExpressionable $ajaxec  Array of jsExpressionable
     * @param string                 $msg     General message, typically won't be displayed
     * @param bool                   $success Was request successful or not
     *
     * @throws Exception\ExitApplicationException
     * @throws \atk4\core\Exception
     */
    public function terminateAjax($ajaxec, $msg = null, $success = true)
    {
        if ($this->canTerminate()) {
            $this->app->terminateJSON(['success' => $success, 'message' => $msg, 'atkjs' => $ajaxec]);
        }
    }

    /**
     * Provided with a $response from callbacks convert it into a JavaScript code.
     *
     * @param array|jsExpressionable $response response from callbacks,
     * @param string                 $chain    JavaScript string
     *
     * @throws Exception
     * @throws \atk4\core\Exception
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

        $ajaxec = implode(";\n", array_map(function (jsExpressionable $r) {
            return $r->jsRender();
        }, $actions));

        return $ajaxec;
    }

    public function getURL($mode = 'callback')
    {
        throw new Exception('Do not use getURL on jsCallback, use getJSURL()');
    }

    /**
     * Transform response into proper js Action and return it.
     *
     * @param View|string|jsExpressionable $response
     *
     * @throws Exception
     * @throws \atk4\core\Exception
     */
    private function _getProperAction($response): jsExpressionable
    {
        $action = null;
        if ($response instanceof View) {
            $action = $this->_jsRenderIntoModal($response);
        } elseif (is_string($response)) {
            $action = new jsExpression('alert([])', [$response]);
        } elseif ($response instanceof jsExpressionable) {
            $action = $response;
        } else {
            throw new Exception(['Incorrect callback. Response must be of type jsExpressionable, View, or String.', 'r' => $response]);
        }

        return $action;
    }

    /**
     * Render View into modal.
     *
     * @throws \atk4\core\Exception
     */
    private function _jsRenderIntoModal(View $response): jsExpressionable
    {
        if ($response instanceof Modal) {
            $html = $response->getHTML();
        } else {
            $modal = new Modal(['id' => false]);
            $modal->add($response);
            $html = $modal->getHTML();
        }

        return new jsExpression('$([html]).modal("show").data("needRemove", true).addClass("atk-callback-response")', ['html' => $html]);
    }
}
