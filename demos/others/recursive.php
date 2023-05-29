<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Button;
use Atk4\Ui\Header;
use Atk4\Ui\Js\Jquery;
use Atk4\Ui\Js\JsReload;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$mySwitcherClass = AnonymousClassNameCache::get_class(fn () => new class() extends View {
    protected function init(): void
    {
        parent::init();

        Header::addTo($this, ['My name is ' . $this->name, 'class.red' => true]);

        $buttons = View::addTo($this, ['ui' => 'basic buttons']);
        Button::addTo($buttons, ['Yellow'])->setAttr('data-id', 'yellow');
        Button::addTo($buttons, ['Blue'])->setAttr('data-id', 'blue');
        Button::addTo($buttons, ['Button'])->setAttr('data-id', 'button');

        $buttons->on('click', '.button', new JsReload($this, [$this->name => (new Jquery())->data('id')]));

        switch ($this->stickyGet($this->name)) {
            case 'yellow':
                self::addTo(View::addTo($this, ['ui' => 'yellow segment']));

                break;
            case 'blue':
                self::addTo(View::addTo($this, ['ui' => 'blue segment']));

                break;
            case 'button':
                Button::addTo(View::addTo($this, ['ui' => 'green segment']), ['Refresh page'])->link([]);

                break;
        }
    }
});

$view = View::addTo($app, ['ui' => 'segment']);

$mySwitcherClass::addTo($view);
