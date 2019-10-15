<?php
/**
 * Created by abelair.
 * Date: 2019-10-15
 * Time: 2:18 p.m.
 */

namespace atk4\ui\ActionExecutor;

use atk4\ui\Modal;

class UserAction extends Modal implements Interface_
{
    public $action = null;

    public function init()
    {
        parent::init();
        $this->observeChanges();


    }

    /**
     * Will associate executor with the action.
     *
     * @param \atk4\data\UserAction\Action $action
     */
    public function setAction(\atk4\data\UserAction\Generic $action)
    {
        $this->action = $action;

        if ($this->action->preview) {

        }

        return $this;
    }

    public function assignTrigger($btn, $arg = [], $when = 'click')
    {
        if ($this->action->enabled) {
            $btn->on($when, $this->jsTrigger($arg));
        } else {
            $btn->addClass('disabled');
        }
    }

    public function jsTrigger($arg = [])
    {
        return $this->show($arg);
    }
}