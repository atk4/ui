<?php

namespace atk4\ui;

/**
 * This class generates action, that will be able to loop-back to the callback method.
 */
class jsModal extends jsExpression
{
    public function __construct($title, $url, $args = [], $mode = 'json')
    {
        if ($url instanceof VirtualPage) {
            $url->needAjax = true;
            $url = $url->getURL('cut');
        }

        parent::__construct('$(this).atkCreateModal([arg])', ['arg' => ['uri' => $url, 'title' => $title, 'mode' => $mode, 'uri_options' => $args]]);
    }
}
