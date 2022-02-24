<?php

declare(strict_types=1);

namespace Atk4\Ui\App;

use Atk4\Ui\Exception;

class SessionManager
{
    /** @var string Session container key. */
    protected $rootNamespace = '__atk_session';

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
        $this->isSessionActive(); // assert session is not disabled

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
        unset($_SESSION);

        if (!$res) {
            throw new Exception('Failed to close a session');
        }
    }

    /**
     * @return mixed
     */
    public function atomicSession(\Closure $fx, bool $readAndCloseImmediately = false)
    {
        $wasActive = $this->isSessionActive();

        if (!$wasActive) {
            $this->startSession($readAndCloseImmediately);
        }

        $e = null;
        try {
            return $fx();
        } catch (\Throwable $e) {
            throw $e;
        } finally {
            if (!$wasActive && !$readAndCloseImmediately) {
                $this->closeSession($e === null);
            }
        }
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
        return $this->atomicSession(function () use ($namespace, $key, $defaultValue) {
            if (!isset($_SESSION[$this->rootNamespace][$namespace][$key])) {
                if ($defaultValue instanceof \Closure) {
                    $defaultValue = $defaultValue($key);
                }

                return $this->memorize($namespace, $key, $defaultValue);
            }

            return $this->recall($namespace, $key);
        });
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
        return $this->atomicSession(function () use ($namespace, $key, $defaultValue) {
            if (!isset($_SESSION[$this->rootNamespace][$namespace][$key])) {
                if ($defaultValue instanceof \Closure) {
                    $defaultValue = $defaultValue($key);
                }

                return $defaultValue;
            }

            return $_SESSION[$this->rootNamespace][$namespace][$key];
        });
    }

    /**
     * Forget session data for $key. If $key is omitted will forget all
     * associated session data.
     *
     * @param string $key Optional key of data to forget
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
