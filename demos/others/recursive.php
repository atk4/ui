<?php

namespace atk4\ui\demo;

require_once __DIR__ . '/../atk-init.php';

class recursive extends \atk4\ui\View
{
    public function init(): void
    {
        parent::init();

        \atk4\ui\Header::addTo($this, ['My name is ' . $this->name, 'red']);

        $buttons = \atk4\ui\View::addTo($this, ['ui' => 'basic buttons']);
        \atk4\ui\Button::addTo($buttons, ['Yellow'])->setAttr('data-id', 'yellow');
        \atk4\ui\Button::addTo($buttons, ['Blue'])->setAttr('data-id', 'blue');
        \atk4\ui\Button::addTo($buttons, ['Button'])->setAttr('data-id', 'button');

        $buttons->on('click', '.button', new \atk4\ui\jsReload($this, [$this->name => (new \atk4\ui\jQuery())->data('id')]));

        switch ($this->app->stickyGet($this->name)) {
        case 'yellow':
            self::addTo(\atk4\ui\View::addTo($this, ['ui' => 'yellow segment']));

            break;
        case 'blue':
            self::addTo(\atk4\ui\View::addTo($this, ['ui' => 'blue segment']));

            break;
        case 'button':
            \atk4\ui\Button::addTo(\atk4\ui\View::addTo($this, ['ui' => 'green segment']), ['Refresh page'])->link([]);

            break;
        }
    }
}

$view = \atk4\ui\View::addTo($app, ['ui' => 'segment']);
MySwitcher::addTo($view);
