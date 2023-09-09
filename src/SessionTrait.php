<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Core\TraitUtil;

trait SessionTrait
{
    private function getSessionManager(): App\SessionManager
    {
        // all methods use this method, so we better check NameTrait existence here in one place
        if (!TraitUtil::hasNameTrait($this)) {
            throw new Exception('Object must have NameTrait applied to use session');
        }

        return $this->getApp()->session;
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
        return $this->getSessionManager()->atomicSession($fx, $readAndCloseImmediately);
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
        return $this->getSessionManager()->memorize($this->name, $key, $value);
    }

    /**
     * Similar to memorize, but if value for key exist, will return it.
     *
     * @param mixed $defaultValue
     *
     * @return mixed Previously memorized data or $defaultValue
     */
    public function learn(string $key, $defaultValue = null)
    {
        return $this->getSessionManager()->learn($this->name, $key, $defaultValue);
    }

    /**
     * Returns session data for this object. If not previously set, then
     * $defaultValue is returned.
     *
     * @param mixed $defaultValue
     *
     * @return mixed Previously memorized data or $defaultValue
     */
    public function recall(string $key, $defaultValue = null)
    {
        return $this->getSessionManager()->recall($this->name, $key, $defaultValue);
    }

    /**
     * Forget session data for $key. If $key is omitted will forget all
     * associated session data.
     *
     * @return $this
     */
    public function forget(string $key = null)
    {
        $this->getSessionManager()->forget($this->name, $key);

        return $this;
    }
}
