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
            //$url = $url->getURL('cut');
            // Not sure if this is the best way...
            $url = $url->cb->owner->jsUrl([$url->cb->urlTrigger => 'cut', '__atk_callback'=>1], (bool) $url->cb->postTrigger);
        }

        parent::__construct('$(this).atkCreateModal([arg])', ['arg' => ['uri' => $url, 'title' => $title, 'mode' => $mode, 'uri_options' => $args]]);
    }
}
