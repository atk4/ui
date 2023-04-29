<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

/**
 * Same as DemosHttpTest, only App::callExit is set to false.
 *
 * @group demos_http
 */
class DemosHttpNoExitTest extends DemosHttpTest
{
    /** @var bool set the app->callExit in demo */
    protected $appCallExit = false;
}
