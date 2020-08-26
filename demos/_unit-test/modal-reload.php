<?php

declare(strict_types=1);

namespace atk4\ui\demo;

use atk4\ui\Button;
use atk4\ui\Header;
use atk4\ui\Modal;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

class ReloadTest extends \atk4\ui\View
{
    protected function init(): void
    {
        parent::init();

        $label = \atk4\ui\Label::addTo($this, ['Testing...', 'detail' => '', 'red']);
        $reload = new \atk4\ui\JsReload($this, [$this->name => 'ok']);

        if (isset($_GET[$this->name])) {
            $label->class[] = 'green';
            $label->content = 'Reload success';
        } else {
            $this->js(true, $reload);
        }
    }
}

// Simulating ModalExecutor reload for Behat test.

Header::addTo($app, ['Testing ModalExecutor reload']);

$modal = Modal::addTo($app->html, ['title' => 'Modal Executor', 'region' => 'Modals']);

$modal->set(function ($modal) {
    ReloadTest::addTo($modal);
});

$button = Button::addTo($app)->set('Test');
$button->on('click', $modal->show());
