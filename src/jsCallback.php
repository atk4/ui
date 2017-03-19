<?php

namespace atk4\ui;

class jsCallback extends Callback
{
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

    public function set($callback, $args = [])
    {
        return parent::set(function () use ($callback) {
            $chain = new jQuery(new jsExpression('this'));
            $response = call_user_func($callback, $chain);

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
