<?php

declare(strict_types=1);

namespace Atk4\Ui\Exception;

class NoRenderTree extends \Atk4\Ui\Exception
{
    public function __construct($object, $action = '')
    {
        parent::__construct('You must use either add($obj) or $obj->invokeInit() before ' . ($action ?: 'performing this action'));
        $this->addMoreInfo('obj', $object);
    }
}
