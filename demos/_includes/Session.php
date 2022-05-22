<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Core\AppScopeTrait;
use Atk4\Core\NameTrait;
use Atk4\Ui\App;
use Atk4\Ui\SessionTrait;

class Session
{
    use AppScopeTrait;
    use NameTrait;
    use SessionTrait;

    public function __construct(App $app)
    {
        $this->name = 'demo';
        $this->setApp($app);
    }
}
