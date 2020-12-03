<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Button;
use Atk4\Ui\Header;
use Atk4\Ui\Modal;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

class ReloadTest extends \Atk4\Ui\View
{
    protected function init(): void
    {
        parent::init();

        $label = \Atk4\Ui\Label::addTo($this, ['Testing...', 'detail' => '', 'red']);
        $reload = new \Atk4\Ui\JsReload($this, [$this->name => 'ok']);

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
