<?php

declare(strict_types=1);

namespace Atk4\Ui\App;

use Atk4\Ui\Exception;

class SessionManager
{
    /** @var string Session container key. */
    protected $rootNamespace = '__atk_session';

    /** @var array<string, array<string, array<string, mixed>>>|null */
    private static $readCache;

    protected function isSessionActive(): bool
    {
        $status = session_status();
        if ($status === \PHP_SESSION_DISABLED) {
            throw new Exception('Session support is disabled');
        }

        return $status === \PHP_SESSION_ACTIVE;
    }

    protected function createStartSessionOptions(): array
    {
        return [];
    }

    protected function startSession(bool $readAndCloseImmediately): void
    {
        $options = $this->createStartSessionOptions();
        if ($readAndCloseImmediately) {
            $options['read_and_close'] = true;
        }

        $res = session_start($options);

        if (!$res) {
            throw new Exception('Failed to start a session');
        }
    }

    protected function closeSession(bool $writeBeforeClose): void
    {
        if ($writeBeforeClose) {
            $res = session_write_close();
        } else {
            $res = session_abort();
        }

        if (!$res) {
            throw new Exception('Failed to close a session');
        }
    }

    /**
     * @template T
     *
     * @param \Closure(): T $fx
     *
     * @return T
     */
    public function atomicSession(\Closure $fx, bool $readAndCloseImmediately = false)
    {
        $wasActive = $this->isSessionActive();

        if (!$wasActive) {
            $this->startSession($readAndCloseImmediately);
        }

        $e = null;
        try {
            self::$readCache = $_SESSION;
            if (!isset(self::$readCache[$this->rootNamespace])) {
                self::$readCache[$this->rootNamespace] = [];
            }

            $res = $fx();

            if (!$readAndCloseImmediately) {
                self::$readCache = $_SESSION;
            }

            return $res;
        } catch (\Throwable $e) {
            throw $e;
        } finally {
            if (!$readAndCloseImmediately && $e !== null) {
                self::$readCache = null;
            }

            if (!$wasActive) {
                if (!$readAndCloseImmediately) {
                    $this->closeSession($e === null);
                }

                unset($_SESSION);
            }
        }
    }

    /**
     * @param bool $found
     *
     * @return mixed
     */
    protected function recallWithCache(string $namespace, string $key, &$found)
    {
        $found = false;

        if (self::$readCache === null) {
            $this->atomicSession(static function (): void {}, true);
        }

        if (isset(self::$readCache[$this->rootNamespace][$namespace])
            && array_key_exists($key, self::$readCache[$this->rootNamespace][$namespace])) {
            $res = self::$readCache[$this->rootNamespace][$namespace][$key];
            $found = true;

            return $res;
        }

        return null;
    }

    /**
     * Returns session data for this object. If not previously set, then
     * $defaultValue is returned.
     *
     * @param mixed $defaultValue
     *
     * @return mixed Previously memorized data or $defaultValue
     */
    public function recall(string $namespace, string $key, $defaultValue = null)
    {
        $res = $this->recallWithCache($namespace, $key, $found);
        if ($found) {
            return $res;
        }

        if ($defaultValue instanceof \Closure) {
            $defaultValue = $defaultValue($key);
        }

        return $defaultValue;
    }

    /**
     * Remember data in object-relevant session data.
     *
     * @param mixed $value
     *
     * @return mixed $value
     */
    public function memorize(string $namespace, string $key, $value)
    {
        return $this->atomicSession(function () use ($namespace, $key, $value) {
            $_SESSION[$this->rootNamespace][$namespace][$key] = $value;

            return $value;
        });
    }

    /**
     * Similar to memorize, but if value for key exist, will return it.
     *
     * @param mixed $defaultValue
     *
     * @return mixed Previously memorized data or $defaultValue
     */
    public function learn(string $namespace, string $key, $defaultValue = null)
    {
        $res = $this->recallWithCache($namespace, $key, $found);
        if ($found) {
            return $res;
        }

        return $this->atomicSession(function () use ($namespace, $key, $defaultValue) {
            $res = $this->recallWithCache($namespace, $key, $found);
            if ($found) {
                return $res;
            }

            if ($defaultValue instanceof \Closure) {
                $defaultValue = $defaultValue($key);
            }

            return $this->memorize($namespace, $key, $defaultValue);
        });
    }

    /**
     * Forget session data for $key. If $key is omitted will forget all
     * associated session data.
     */
    public function forget(string $namespace, string $key = null): void
    {
        $this->atomicSession(function () use ($namespace, $key) {
            if ($key === null) {
                unset($_SESSION[$this->rootNamespace][$namespace]);
            } else {
                unset($_SESSION[$this->rootNamespace][$namespace][$key]);
            }
        });
    }
}
