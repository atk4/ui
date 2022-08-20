<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Button;
use Atk4\Ui\Header;
use Atk4\Ui\Modal;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

// simulating ModalExecutor reload for Behat test

Header::addTo($app, ['Testing ModalExecutor reload']);

$modal = Modal::addTo($app, ['title' => 'Modal Executor']);

$modal->set(function ($modal) {
    ReloadTest::addTo($modal);
});

$button = Button::addTo($app)->set('Test');
$button->on('click', $modal->show());
