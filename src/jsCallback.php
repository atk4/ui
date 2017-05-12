<?php

namespace atk4\ui;

class jsCallback extends Callback implements jsExpressionable
{
    public $args = [];

    public $confirm = null;

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

        return (new jQuery())->ajaxec([
            'uri'        => $this->getURL(),
            'uri_options'=> $this->args,
            'confirm'    => $this->confirm,
        ])->jsRender();
    }

    public function setConfirm($text = 'Are you sure?')
    {
        $this->confirm = $text;
    }

    public function set($callback, $args = [])
    {
        $this->args = [];

        foreach ($args as $key=>$val) {
            if (is_numeric($key)) {
                $key = 'c'.$key;
            }
            $this->args[$key] = $val;
        }

        return parent::set(function () use ($callback) {
            $chain = new jQuery(new jsExpression('this'));

            $values = [];
            foreach ($this->args as $key=>$value) {
                $values[] = isset($_POST[$key]) ? $_POST[$key] : null;
            }

            $response = call_user_func_array($callback, array_merge([$chain], $values));

            if ($response === $chain) {
                $response = null;
            }

            $actions = [];

            if ($chain->_chain) {
                $actions[] = $chain;
            }

            $response = $this->flatternArray($response);

            foreach ($response as $r) {
                if (is_string($r)) {
                    $actions[] = new jsExpression('alert([])', [$r]);
                } elseif ($r instanceof jsExpressionable) {
                    $actions[] = $r;
                } elseif ($r === null) {
                    continue;
                } else {
                    throw new Exception(['Incorrect callback. Must be string or action.', 'r'=>$r]);
                }
            }

            $ajaxec = implode(";\n", array_map(function (jsExpressionable $r) {
                return $r->jsRender();
            }, $actions));

            $this->app->terminate(json_encode(['success'=>true, 'message'=>'Success', 'eval'=>$ajaxec]));
        });
    }
}
