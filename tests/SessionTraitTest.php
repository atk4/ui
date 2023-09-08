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
 *
 * @runTestsInSeparateProcesses
 */
class SessionTraitTest extends TestCase
{
    use CreateAppTrait;

    protected function setUp(): void
    {
        parent::setUp();

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
        $m = new SessionWithoutNameMock($this->createApp());

        // when try to start session without NameTrait
        $this->expectException(Exception::class);
        $m->memorize('test', 'foo');
    }

    public function testConstructor(): void
    {
        $m = new SessionMock($this->createApp());

        self::assertFalse(isset($_SESSION));
        $m->atomicSession(static function (): void {
            self::assertTrue(isset($_SESSION));
        });
        self::assertFalse(isset($_SESSION));
    }

    public function testMemorize(): void
    {
        $m = new SessionMock($this->createApp());
        $m->name = 'test';

        // value as string
        $m->memorize('foo', 'bar');
        $m->atomicSession(static function () use ($m): void {
            self::assertSame('bar', $_SESSION['__atk_session'][$m->name]['foo']);
        }, true);

        // value as null
        $m->memorize('foo', null);
        $m->atomicSession(static function () use ($m): void {
            self::assertNull($_SESSION['__atk_session'][$m->name]['foo']);
        }, true);

        // value as object
        $o = new \stdClass();
        $o->foo = 'x';
        $m->memorize('foo', $o);
        $m->atomicSession(static function () use ($m, $o): void {
            self::assertSame(serialize($o), serialize($_SESSION['__atk_session'][$m->name]['foo']));
        }, true);
    }

    public function testLearnRecallForget(): void
    {
        $m = new SessionMock($this->createApp());
        $m->name = 'test';

        // value as string
        $m->learn('foo', 'bar');
        self::assertSame('bar', $m->recall('foo'));

        $m->learn('foo', 'qwerty');
        self::assertSame('bar', $m->recall('foo'));

        $m->forget('foo');
        self::assertSame('undefined', $m->recall('foo', 'undefined'));

        // value as callback
        $m->learn('foo', static function (string $key) {
            return $key . '_bar';
        });
        self::assertSame('foo_bar', $m->recall('foo'));

        $m->learn('foo_2', 'another');
        self::assertSame('another', $m->recall('foo_2'));

        $v = $m->recall('foo_3', static function (string $key) {
            return $key . '_bar';
        });
        self::assertSame('foo_3_bar', $v);
        self::assertSame('undefined', $m->recall('foo_3', 'undefined'));

        $m->forget();
        self::assertSame('undefined', $m->recall('foo', 'undefined'));
        self::assertSame('undefined', $m->recall('foo_2', 'undefined'));
        self::assertSame('undefined', $m->recall('foo_3', 'undefined'));
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

class SessionWithoutNameMock extends SessionAbstractMock {}
