<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\AppScopeTrait;
use Atk4\Core\NameTrait;
use Atk4\Core\Phpunit\TestCase;
use Atk4\Ui\App;
use Atk4\Ui\Exception;
use Atk4\Ui\SessionTrait;

/**
 * @group require_session
 * @runTestsInSeparateProcesses
 */
class SessionTraitTest extends TestCase
{
    /** @var App */
    protected $app;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app = new App([
            'catch_exceptions' => false,
            'always_run' => false,
        ]);

        session_abort();
        $sessionDir = sys_get_temp_dir() . '/atk4_test__ui__session';
        if (!file_exists($sessionDir)) {
            mkdir($sessionDir);
        }
        ini_set('session.save_path', $sessionDir);
    }

    protected function tearDown(): void
    {
        session_abort();
        $sessionDir = ini_get('session.save_path');
        foreach (scandir($sessionDir) as $f) {
            if (!in_array($f, ['.', '..'], true)) {
                unlink($sessionDir . '/' . $f);
            }
        }

        rmdir($sessionDir);

        parent::tearDown();
    }

    public function testException1(): void
    {
        // when try to start session without NameTrait
        $this->expectException(Exception::class);
        $m = new SessionWithoutNameMock($this->app);
        $m->memorize('test', 'foo');
    }

    public function testConstructor(): void
    {
        $m = new SessionMock($this->app);

        $this->assertFalse(isset($_SESSION));
        $m->getApp()->session->startSession();
        $this->assertTrue(isset($_SESSION));
        $m->getApp()->session->destroySession();
        $this->assertFalse(isset($_SESSION));
    }

    /**
     * Test memorize().
     */
    public function testMemorize(): void
    {
        $m = new SessionMock($this->app);
        $m->name = 'test';

        // value as string
        $m->memorize('foo', 'bar');
        $this->assertSame('bar', $_SESSION['__atk_session'][$m->name]['foo']);

        // value as null
        $m->memorize('foo', null);
        $this->assertNull($_SESSION['__atk_session'][$m->name]['foo']);

        // value as object
        $o = new \stdClass();
        $m->memorize('foo', $o);
        $this->assertSame($o, $_SESSION['__atk_session'][$m->name]['foo']);

        $m->getApp()->session->destroySession();
    }

    /**
     * Test learn(), recall(), forget().
     */
    public function testLearnRecallForget(): void
    {
        $m = new SessionMock($this->app);
        $m->name = 'test';

        // value as string
        $m->learn('foo', 'bar');
        $this->assertSame('bar', $m->recall('foo'));

        $m->learn('foo', 'qwerty');
        $this->assertSame('bar', $m->recall('foo'));

        $m->forget('foo');
        $this->assertSame('undefined', $m->recall('foo', 'undefined'));

        // value as callback
        $m->learn('foo', function ($key) {
            return $key . '_bar';
        });
        $this->assertSame('foo_bar', $m->recall('foo'));

        $m->learn('foo_2', 'another');
        $this->assertSame('another', $m->recall('foo_2'));

        $v = $m->recall('foo_3', function ($key) {
            return $key . '_bar';
        });
        $this->assertSame('foo_3_bar', $v);
        $this->assertSame('undefined', $m->recall('foo_3', 'undefined'));

        $m->forget();
        $this->assertSame('undefined', $m->recall('foo', 'undefined'));
        $this->assertSame('undefined', $m->recall('foo_2', 'undefined'));
        $this->assertSame('undefined', $m->recall('foo_3', 'undefined'));
    }
}

abstract class SessionAbstractMock
{
    use AppScopeTrait;
    use SessionTrait;

    public function __construct(App $app)
    {
        $this->setApp($app);
    }
}

class SessionMock extends SessionAbstractMock
{
    use NameTrait;
}

class SessionWithoutNameMock extends SessionAbstractMock
{
}
