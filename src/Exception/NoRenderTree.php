<?php

namespace atk4\ui\Exception;

class NoRenderTree extends \atk4\ui\Exception
{
    public function __construct($object, $action = '')
    {
        parent::__construct(['You must use either add($obj) or $obj->init() before '.($action ?: 'performing this action'), 'obj' => $object]);
    }
}
