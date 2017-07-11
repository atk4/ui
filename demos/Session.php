<?php

/***
 * Temp Class Session
 * Copied from previous ATK4 4.3
 */

class Session
{
    public $name;
    public $_is_session_initialized = false;

    public function __construct($name = 'atk4')
    {
        $this->name = $name;
        $this->initializeSession();
    }

    // {{{ Session management: http://agiletoolkit.org/doc/session
    /**
     * Remember data in object-relevant session data
     *
     * @param string $key   Key for the data
     * @param mixed  $value Value
     *
     * @return mixed $value
     */
    public function memorize($key, $value)
    {
        if (!session_id()) {
            $this->initializeSession();
        }

        if ($value instanceof Model) {
            unset($_SESSION['o'][$this->name][$key]);
            $_SESSION['s'][$this->name][$key] = serialize($value);

            return $value;
        }

        unset($_SESSION['s'][$this->name][$key]);
        $_SESSION['o'][$this->name][$key] = $value;

        return $value;
    }

    /**
     * Similar to memorize, but if value for key exist, will return it.
     *
     * @param string $key     Data Key
     * @param mixed  $default Default value
     *
     * @return mixed Previously memorized data or $default
     */
    public function learn($key, $default = null)
    {
        if (!session_id()) {
            $this->initializeSession(false);
        }

        if (!isset($_SESSION['o'][$this->name][$key])
            || is_null($_SESSION['o'][$this->name][$key])
        ) {
            if (is_callable($default)) {
                $default = call_user_func($default);
            }

            return $this->memorize($key, $default);
        } else {

            return $this->recall($key);
        }
    }

    /**
     * Forget session data for arg $key. If $key is omitted will forget all
     * associated session data.
     *
     * @param string $key Optional key of data to forget
     *
     * @return AbstractObject $this
     */
    public function forget($key = null)
    {
        if (!session_id()) {
            $this->initializeSession(false);
        }

        if (is_null($key)) {
            unset ($_SESSION['o'][$this->name]);
            unset ($_SESSION['s'][$this->name]);
        } else {
            unset ($_SESSION['o'][$this->name][$key]);
            unset ($_SESSION['s'][$this->name][$key]);
        }

        return $this;
    }

    /**
     * Returns session data for this object. If not previously set, then
     * $default is returned.
     *
     * @param string $key     Data Key
     * @param mixed  $default Default value
     *
     * @return mixed Previously memorized data or $default
     */
    public function recall($key, $default = null)
    {
        if (!session_id()) {
            $this->initializeSession(false);
        }

        if (!isset($_SESSION['o'][$this->name][$key])
            || is_null($_SESSION['o'][$this->name][$key])
        ) {
            if (!isset($_SESSION['s'][$this->name][$key])) {

                return $default;
            }
            $v = $this->add(unserialize($_SESSION['s'][$this->name][$key]));
            $v->init();

            return $v;
        }

        return $_SESSION['o'][$this->name][$key];
    }

    public function initializeSession($create=true)
    {
        /* Attempts to re-initialize session. If session is not found,
           new one will be created, unless $create is set to false. Avoiding
           session creation and placing cookies is to enhance user privacy.
        Call to memorize() / recall() will automatically create session */

        if ($this->_is_session_initialized || session_id()) {

            return;
        }

        // Change settings if defined in settings file
        $params=session_get_cookie_params();

        $params['httponly'] = true;   // true by default

        if($create===false && !isset($_COOKIE[$this->name]))return;
        $this->_is_session_initialized=true;
        session_set_cookie_params(
            $params['lifetime'],
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
        session_name($this->name);
        session_start();
    }

    /** Completely destroy existing session */
    public function destroySession()
    {
        if ($this->_is_session_initialized) {
            $_SESSION = array();
            if (isset($_COOKIE[$this->name])) {
                setcookie($this->name, '', time()-42000, '/');
            }
            session_destroy();
            $this->_is_session_initialized = false;
        }
    }
}
