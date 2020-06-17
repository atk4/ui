<?php

declare(strict_types=1);

namespace atk4\ui\tests;

/**
 * Same as DemosHttpTest, only App::call_exit is set to false.
 *
 * @group demosHttp
 */
class DemosHttpNoExitTest extends DemosHttpTest
{
    /** @var bool set the app->call_exit in demo */
    protected static $app_def_call_exit = false;
}
