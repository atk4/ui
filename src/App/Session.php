<?php

declare(strict_types=1);

namespace Atk4\Ui\App;

class Session
{
    /** @var string Session container key. */
    protected $session_key = '__atk_session';

    /**
     * Create new session.
     *
     * @param array $options Options for session_start()
     */
    public function startSession(array $options = []): void
    {
        switch (session_status()) {
            case \PHP_SESSION_DISABLED:
                // @codeCoverageIgnoreStart - impossible to test
                throw new Exception('Sessions are disabled on server');
                // @codeCoverageIgnoreEnd
            case \PHP_SESSION_NONE:
                session_start($options);

                break;
        }
    }

    /**
     * Destroy existing session.
     */
    public function destroySession(): void
    {
        if (session_status() === \PHP_SESSION_ACTIVE) {
            session_destroy();
            unset($_SESSION);
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
        $this->startSession();

        $_SESSION[$this->session_key][$namespace][$key] = $value;

        return $value;
    }

    /**
     * Similar to memorize, but if value for key exist, will return it.
     *
     * @param mixed $default
     *
     * @return mixed Previously memorized data or $default
     */
    public function learn(string $namespace, string $key, $default = null)
    {
        $this->startSession();

        if (!isset($_SESSION[$this->session_key][$namespace][$key])) {
            if ($default instanceof \Closure) {
                $default = $default($key);
            }

            return $this->memorize($key, $default);
        }

        return $this->recall($key);
    }

    /**
     * Returns session data for this object. If not previously set, then
     * $default is returned.
     *
     * @param mixed $default
     *
     * @return mixed Previously memorized data or $default
     */
    public function recall(string $namespace, string $key, $default = null)
    {
        $this->startSession();

        if (!isset($_SESSION[$this->session_key][$namespace][$key])) {
            if ($default instanceof \Closure) {
                $default = $default($key);
            }

            return $default;
        }

        return $_SESSION[$this->session_key][$namespace][$key];
    }

    /**
     * Forget session data for $key. If $key is omitted will forget all
     * associated session data.
     *
     * @param string $key Optional key of data to forget
     */
    public function forget(string $namespace, string $key = null): void
    {
        $this->startSession();

        if ($key === null) {
            unset($_SESSION[$this->session_key][$namespace]);
        } else {
            unset($_SESSION[$this->session_key][$namespace][$key]);
        }
    }
}
