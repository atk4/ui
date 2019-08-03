<?php

namespace atk4\ui\tests;

use atk4\core\Exception;

/**
 * Making sure demo pages don't throw exceptions and coverage is
 * handled.
 */
class DemoCallExitExceptionTest extends DemoCallExitTest
{
    /** @var bool set the app->call_exit in demo */
    protected static $app_def_call_exit  = false;
}
