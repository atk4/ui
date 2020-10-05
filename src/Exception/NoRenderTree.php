<?php

declare(strict_types=1);

namespace atk4\ui\Exception;

class NoRenderTree extends \atk4\ui\Exception
{
    public function __construct($object, $action = '')
    {
        parent::__construct('You must use either add($obj) or $obj->invokeInit() before ' . ($action ?: 'performing this action'));
        $this->addMoreInfo('obj', $object);
    }
}
