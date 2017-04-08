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

        $final = (new jQuery())
          ->modal([
            'header'  => $title,
            'content' => $args['content'],
            'uri'     => $url,
          ]);

        $final->_constructorArgs = null;
        $final->_library .= '.ATK';

        parent::__construct($final->jsRender());
    }
}
