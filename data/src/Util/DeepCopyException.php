<?php

declare(strict_types=1);

namespace Atk4\Data\Util;

class DeepCopyException extends \Atk4\Data\Exception
{
    /**
     * @return $this
     */
    public function addDepth(string $prefix)
    {
        $this->addMoreInfo('depth', $prefix . ':' . $this->getParams()['depth']);

        return $this;
    }
}
