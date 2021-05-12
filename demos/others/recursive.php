<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

/** @var \Atk4\Ui\View $mySwitcherClass */
$mySwitcherClass = get_class(new class() extends \Atk4\Ui\View {
    protected function init(): void
    {
        parent::init();

        \Atk4\Ui\Header::addTo($this, ['My name is ' . $this->name, 'red']);

        $buttons = \Atk4\Ui\View::addTo($this, ['ui' => 'basic buttons']);
        \Atk4\Ui\Button::addTo($buttons, ['Yellow'])->setAttr('data-id', 'yellow');
        \Atk4\Ui\Button::addTo($buttons, ['Blue'])->setAttr('data-id', 'blue');
        \Atk4\Ui\Button::addTo($buttons, ['Button'])->setAttr('data-id', 'button');

        $buttons->on('click', '.button', new \Atk4\Ui\JsReload($this, [$this->name => (new \Atk4\Ui\Jquery())->data('id')]));

        switch ($this->getApp()->stickyGet($this->name)) {
            case 'yellow':
                self::addTo(\Atk4\Ui\View::addTo($this, ['ui' => 'yellow segment']));

                break;
            case 'blue':
                self::addTo(\Atk4\Ui\View::addTo($this, ['ui' => 'blue segment']));

                break;
            case 'button':
                \Atk4\Ui\Button::addTo(\Atk4\Ui\View::addTo($this, ['ui' => 'green segment']), ['Refresh page'])->link([]);

                break;
        }
    }
});

$view = \Atk4\Ui\View::addTo($app, ['ui' => 'segment']);

$mySwitcherClass::addTo($view);
