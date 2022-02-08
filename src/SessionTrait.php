<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Core\TraitUtil;

trait SessionTrait
{
    private function getSession(): App\Session
    {
        // all methods use this method, so we better check NameTrait existence here in one place
        if (!TraitUtil::hasNameTrait($this)) {
            throw new Exception('Object should have NameTrait applied to use session');
        }

        return $this->getApp()->session;
    }

    /**
     * Remember data in object-relevant session data.
     *
     * @param mixed $value
     *
     * @return mixed $value
     */
    public function memorize(string $key, $value)
    {
        return $this->getSession()->memorize($this->name, $key, $value);
    }

    /**
     * Similar to memorize, but if value for key exist, will return it.
     *
     * @param mixed $default
     *
     * @return mixed Previously memorized data or $default
     */
    public function learn(string $key, $default = null)
    {
        return $this->getSession()->learn($this->name, $key, $default);
    }

    /**
     * Returns session data for this object. If not previously set, then
     * $default is returned.
     *
     * @param mixed $default
     *
     * @return mixed Previously memorized data or $default
     */
    public function recall(string $key, $default = null)
    {
        return $this->getSession()->recall($this->name, $key, $default);
    }

    /**
     * Forget session data for $key. If $key is omitted will forget all
     * associated session data.
     *
     * @param string $key Optional key of data to forget
     *
     * @return $this
     */
    public function forget(string $key = null)
    {
        $this->getSession()->forget($this->name, $key);

        return $this;
    }
}
