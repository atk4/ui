<?php

namespace atk4\ui;

/**
 * This class generates action, that will be able to loop-back to the callback method.
 */
class jsModal extends jsExpression
{
    public function __construct($title, $url, $args = [])
    {
        if ($url instanceof VirtualPage) {
            $url = $url->getURL('cut');
        }
        $args = array_merge($args, ['json'=>true]);
        parent::__construct('$(this).createModal([arg])', ['arg'=>['uri'=>$url, 'title'=>$title, 'uri_options'=>$args]]);
    }
}
