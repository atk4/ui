<?php

include 'init.php';

class MySwitcher extends \atk4\ui\View
{
    public function init()
    {
        parent::init();

        $this->add(['Header', 'My name is '.$this->name, 'red']);

        $buttons = $this->add(['ui' => 'basic buttons']);
        $buttons->add(['Button', 'Yellow'])->setAttr('data-id', 'yellow');
        $buttons->add(['Button', 'Blue'])->setAttr('data-id', 'blue');
        $buttons->add(['Button', 'Button'])->setAttr('data-id', 'button');

        $buttons->on('click', '.button', new \atk4\ui\jsReload($this, [$this->name => (new \atk4\ui\jQuery())->data('id')]));

        switch ($this->app->stickyGet($this->name)) {
        case 'yellow':
            $this->add(['ui' => 'yellow segment'])->add(new self());
            break;
        case 'blue':
            $this->add(['ui' => 'blue segment'])->add(new self());
            break;
        case 'button':
            $this->add(['ui' => 'green segment'])->add(['Button', 'Refresh page'])->link([]);
            break;
        }
    }
}

$app->add(['ui' => 'segment'])->add(new MySwitcher());
