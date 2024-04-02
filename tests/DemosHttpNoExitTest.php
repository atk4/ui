<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use PHPUnit\Framework\Attributes\Group;

/**
 * Same as DemosHttpTest, only App::callExit is set to false.
 *
 * @group demos_http
 */
#[Group('demos_http')]
class DemosHttpNoExitTest extends DemosHttpTest
{
    /** @var bool set the app->callExit in demo */
    protected $appCallExit = false;
}
